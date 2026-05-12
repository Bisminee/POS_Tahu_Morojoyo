<?php

namespace App\Http\Controllers;

use App\Models\Harga;
use App\Models\Menu;
use App\Models\StokPcs;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function pos(Request $request)
    {
        $menus = Menu::with(['menuDetails.pcsTahu', 'hargas'])->get();

        $paymentMethods = [
            'normal',
            'gofood',
            'shopeefood',
        ];

        $stocks = StokPcs::with('pcsTahu')->get();

        $receipt = null;

        if ($request->session()->has('receipt_id')) {
            $receipt = Transaction::with('items')
                ->find($request->session()->get('receipt_id'));
        }

        return view('cashier.pos', compact(
            'menus',
            'paymentMethods',
            'stocks',
            'receipt'
        ));
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'payment_method' => ['required', 'string'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'cart' => ['required', 'string'],
        ]);

        $cart = json_decode($data['cart'], true);

        // VALIDASI KERANJANG
        if (!is_array($cart) || count($cart) === 0) {

            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong. Tambahkan menu sebelum checkout.'
            ], 400);
        }

        $subTotal = 0;
        $items = [];
        $missingStock = [];

        foreach ($cart as $row) {

            $quantity = intval($row['qty'] ?? 0);

            if ($quantity <= 0) {
                continue;
            }

            // MENU CUSTOM
            if (!empty($row['is_custom'])) {

                $unitPrice = floatval($row['unit_price'] ?? 0);
                $subtotal = $unitPrice * $quantity;

                $items[] = [
                    'menu_id' => null,
                    'nama_item' => trim($row['name'] ?? 'Custom menu'),
                    'qty' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'is_custom' => true,
                ];

                $subTotal += $subtotal;

                continue;
            }

            // VALIDASI MENU
            if (empty($row['menu_id'])) {
                continue;
            }

            $menu = Menu::with([
                'menuDetails.pcsTahu',
                'hargas'
            ])->find($row['menu_id']);

            if (!$menu) {
                continue;
            }

            $hargaModel = $menu->hargas->first();

            if (!$hargaModel) {
                continue;
            }

            // PILIH HARGA BERDASARKAN METODE PEMBAYARAN
            $unitPrice = match ($data['payment_method']) {
                'gofood' => $hargaModel->harga_gofood ?? 0,
                'shopeefood' => $hargaModel->harga_shopeefood ?? 0,
                default => $hargaModel->harga_normal ?? 0,
            };

            $subtotal = $unitPrice * $quantity;

            $items[] = [
                'menu_id' => $menu->idMenu,
                'nama_item' => $menu->namaMenu,
                'qty' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'is_custom' => false,
            ];

            // CEK STOK
            foreach ($menu->menuDetails as $detail) {

                $need = intval($detail->jumlah_pcs) * $quantity;

                $stok = StokPcs::where(
                    'id_pcs_tahu',
                    $detail->id_pcs
                )->orderBy('idStokPcs')->first();

                if (!$stok || $stok->jumlah_stok < $need) {

                    $missingStock[] =
                        $detail->pcsTahu?->nama_pcs
                        ?: 'Bahan tidak dikenal';
                }
            }

            $subTotal += $subtotal;
        }

        // JIKA STOK TIDAK CUKUP
        if (count($missingStock) > 0) {

            $message = 'Stok tidak cukup untuk: '
                . implode(', ', array_unique($missingStock));

            return response()->json([
                'success' => false,
                'message' => $message
            ], 400);
        }

        $discount = floatval($data['discount'] ?? 0);

        $total = max(0, $subTotal - $discount);

        DB::beginTransaction();

        try {

            // SIMPAN TRANSAKSI
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'payment_method' => $data['payment_method'],
                'discount' => $discount,
                'sub_total' => $subTotal,
                'total' => $total,
                'status' => 'completed',
            ]);

            // SIMPAN ITEM TRANSAKSI
            foreach ($items as $itemData) {

                TransactionItem::create(array_merge(
                    $itemData,
                    [
                        'transaction_id' => $transaction->id,
                    ]
                ));
            }

            // KURANGI STOK
            foreach ($items as $itemData) {

                if (
                    $itemData['is_custom']
                    || !$itemData['menu_id']
                ) {
                    continue;
                }

                $menu = Menu::with('menuDetails')
                    ->find($itemData['menu_id']);

                foreach ($menu->menuDetails as $detail) {

                    $need = intval($detail->jumlah_pcs)
                        * $itemData['qty'];

                    $stok = StokPcs::where(
                        'id_pcs_tahu',
                        $detail->id_pcs
                    )->orderBy('idStokPcs')->first();

                    if ($stok) {
                        $stok->jumlah_stok = max(0, $stok->jumlah_stok - $need);
                        $stok->save();
                    }
                }
            }

            DB::commit();

            // RESPONSE BERHASIL
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan.',
                'id' => $transaction->id,
            ]);
        } catch (\Throwable $ex) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan transaksi.',
                'error' => $ex->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
