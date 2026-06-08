<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Shift;
use App\Models\StokPcs;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function showLoginForm()
    {
        return view('cashier.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->forget(['selected_shift_id', 'selected_cashier_name']);

        if (Auth::user()->role !== 'kasir') {
            Auth::logout();

            return back()
                ->withErrors(['email' => 'Hanya kasir yang dapat masuk ke POS.'])
                ->onlyInput('email');
        }

        return redirect()->route('attendance.index');
    }

    protected function getSelectedShift(): ?Shift
    {
        $shiftId = session('selected_shift_id');
        if ($shiftId) {
            /** @var Shift|null */
            return Shift::with('karyawan')->find((int) $shiftId);
        }
        return null;
    }

    protected function setSelectedShift(Shift $shift): void
    {
        session()->put('selected_shift_id', $shift->id);

        session()->put(
            'selected_cashier_name',
            $shift->karyawan?->nama ?? 'Kasir'
        );

        session()->save();
    }


    protected function getActiveCashierUserId(): int
    {
        return Auth::id();
    }

    public function pos(Request $request)
    {
        $menus = Menu::with(['menuDetails.pcsTahu', 'hargas'])->get();
        $paymentMethods = ['cash', 'qris', 'gofood', 'shopeefood'];
        $stocks = StokPcs::with('pcsTahu')->get()->keyBy(fn($s) => $s->pcsTahu?->id_pcs ?? $s->id_pcs);

        $receipt = null;
        if ($request->session()->has('receipt_id')) {
            $receipt = Transaction::with('items')->find($request->session()->get('receipt_id'));
        }

        // Gunakan Carbon::today() dengan timezone yang sama dengan APP_TIMEZONE
        $today = \Carbon\Carbon::today(config('app.timezone'));

        $todayTransactions = Transaction::whereDate('created_at', $today)->count();
        $todaySales        = Transaction::whereDate('created_at', $today)->sum('total');
        $todayItems        = TransactionItem::whereHas(
            'transaction',
            fn($q) =>
            $q->whereDate('created_at', $today)
        )->sum('qty');

        $selectedShift = $this->getSelectedShift();

        return view('cashier.pos', compact(
            'menus',
            'paymentMethods',
            'stocks',
            'receipt',
            'todaySales',
            'todayTransactions',
            'todayItems',
            'selectedShift'
        ));
    }

    public function showShiftSelection()
    {
        $todayShifts = Shift::with(['karyawan', 'cabang'])
            ->whereDate('tanggal', today())
            ->get();

        $shift = $this->getSelectedShift();

        return view('cashier.shift-selection', [
            'todayShifts' => $todayShifts,
            'shift' => $shift,
        ]);
    }

    public function selectShift(Request $request)
    {
        $request->validate(['shift_id' => 'required|exists:shifts,id']);

        $shift = Shift::with(['karyawan', 'cabang'])->find($request->shift_id);
        if (!$shift) {
            return redirect()->route('cashier.select-shift')
                ->withErrors(['shift_id' => 'Shift tidak ditemukan.']);
        }

        $this->setSelectedShift($shift);

        return redirect()->route('cashier.select-shift')
            ->with('status', 'Shift dipilih. Silakan lanjutkan verifikasi wajah.');
    }


    public function checkout(Request $request)
    {
        $data = $request->validate([
            'payment_method' => ['required', 'string'],
            'discount'       => ['nullable', 'numeric', 'min:0'],
            'cart'           => ['required', 'string'],
        ]);

        $cart = json_decode($data['cart'], true);

        if (!is_array($cart) || count($cart) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong.',
            ], 400);
        }

        $subTotal     = 0;
        $items        = [];
        $missingStock = [];

        foreach ($cart as $row) {

            $quantity = intval($row['qty'] ?? 0);
            if ($quantity <= 0) continue;

            // FIX Bug 1: frontend pakai key 'custom', bukan 'is_custom'
            $isCustom = !empty($row['custom']);

            // MENU CUSTOM
            if ($isCustom) {
                // FIX Bug 1: frontend pakai key 'unitPrice', bukan 'unit_price'
                $unitPrice = floatval($row['unitPrice'] ?? $row['unit_price'] ?? 0);
                $subtotal  = $unitPrice * $quantity;

                $items[] = [
                    'menu_id'   => null,
                    'nama_item' => trim($row['name'] ?? 'Custom menu'),
                    'qty'       => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal'  => $subtotal,
                    'is_custom' => true,
                ];

                $subTotal += $subtotal;
                continue;
            }

            $menuId = $row['menuId'] ?? $row['menu_id'] ?? null;
            if (!$menuId) continue;

            $menu = Menu::with(['menuDetails.pcsTahu', 'hargas'])->find($menuId);
            if (!$menu) continue;

            // Struktur tabel hargas yang baru sudah tidak memakai kolom
            // metode_payment dan harga. Sekarang 1 menu memiliki 1 baris harga
            // dengan kolom: harga_normal, harga_gofood, harga_shopeefood.
            $hargaModel = $menu->hargas->first();

            if (!$hargaModel) {
                continue;
            }

            $unitPrice = match ($data['payment_method']) {
                'gofood'     => floatval($hargaModel->harga_gofood ?? 0),
                'shopeefood' => floatval($hargaModel->harga_shopeefood ?? 0),
                default      => floatval($hargaModel->harga_normal ?? 0),
            };

            if ($unitPrice <= 0) {
                continue;
            }

            $subtotal = $unitPrice * $quantity;

            $items[] = [
                'menu_id'   => $menu->idMenu,
                'nama_item' => $menu->namaMenu,
                'qty'       => $quantity,
                'unit_price' => $unitPrice,
                'subtotal'  => $subtotal,
                'is_custom' => false,
            ];

            // CEK STOK
            foreach ($menu->menuDetails as $detail) {
                $need = intval($detail->jumlah_pcs) * $quantity;
                $stok = StokPcs::where('id_pcs_tahu', $detail->id_pcs)
                    ->orderBy('idStokPcs')
                    ->first();

                if (!$stok || $stok->jumlah_stok < $need) {
                    $missingStock[] = $detail->pcsTahu?->nama_pcs ?: 'Bahan tidak dikenal';
                }
            }

            $subTotal += $subtotal;
        }

        if (count($missingStock) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak cukup untuk: ' . implode(', ', array_unique($missingStock)),
            ], 400);
        }

        $discount = floatval($data['discount'] ?? 0);
        $total    = max(0, $subTotal - $discount);

        DB::beginTransaction();

        try {
            $transaction = Transaction::create([
                'user_id'        => $this->getActiveCashierUserId(),
                'payment_method' => $data['payment_method'],
                'discount'       => $discount,
                'sub_total'      => $subTotal,
                'total'          => $total,
                'status'         => 'completed',
            ]);

            foreach ($items as $itemData) {
                TransactionItem::create(array_merge($itemData, [
                    'transaction_id' => $transaction->id,
                ]));
            }

            // KURANGI STOK
            foreach ($items as $itemData) {
                if ($itemData['is_custom'] || !$itemData['menu_id']) continue;

                $menu = Menu::with('menuDetails')->find($itemData['menu_id']);
                foreach ($menu->menuDetails as $detail) {
                    $need = intval($detail->jumlah_pcs) * $itemData['qty'];
                    $stok = StokPcs::where('id_pcs_tahu', $detail->id_pcs)
                        ->orderBy('idStokPcs')
                        ->first();

                    if ($stok) {
                        $stok->jumlah_stok = max(0, $stok->jumlah_stok - $need);
                        $stok->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan.',
                'id'      => $transaction->id,
            ]);
        } catch (\Throwable $ex) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan transaksi.',
                'error'   => $ex->getMessage(),
            ], 500);
        }
    }

    public function syncToSheets(Request $req)
    {
        try {
            $keyPath = storage_path('app/google-service-account.json');

            if (!file_exists($keyPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File service account tidak ditemukan di storage/app/google-service-account.json'
                ], 500);
            }

            $spreadsheetId = $req->input('spreadsheet_id');
            if (!$spreadsheetId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Spreadsheet ID tidak boleh kosong.'
                ], 400);
            }

            // ── Setup Google Client ──
            $client = new \Google\Client();
            $client->setAuthConfig($keyPath);
            $client->addScope(\Google\Service\Sheets::SPREADSHEETS);
            $service = new \Google\Service\Sheets($client);

            // ── Nama cabang untuk prefix tab ──
            $user       = Auth::user();
            $cabangName = $user->cabang?->namaCabang ?? 'Cabang';

            $tabRingkasan = "Ringkasan Harian - {$cabangName}";
            $tabDetail    = "Detail Transaksi - {$cabangName}";
            $tabMutasi    = "Mutasi Stok - {$cabangName}";

            // ════════════════════════════════════════════
            // TAB 1 — "Ringkasan Harian - {Cabang}"
            // ════════════════════════════════════════════
            $this->ensureSheetHeader($service, $spreadsheetId, $tabRingkasan, [
                'Tanggal',
                'Cabang',
                'Kasir',
                'Jumlah Transaksi',
                'Item Terjual',
                'Total Diskon',
                'Total Penjualan'
            ]);

            $ringkasan = $req->input('ringkasan', []);
            $tanggal   = $ringkasan['tanggal'] ?? '';

            $existing     = $service->spreadsheets_values->get($spreadsheetId, $tabRingkasan . '!A:A');
            $existingRows = $existing->getValues() ?? [];

            $targetRow = null;
            foreach ($existingRows as $rowIdx => $rowData) {
                if (($rowData[0] ?? '') === $tanggal && $rowIdx > 0) {
                    $targetRow = $rowIdx + 1;
                    break;
                }
            }

            $ringkasanRow = new \Google\Service\Sheets\ValueRange([
                'values' => [[
                    $tanggal,
                    $cabangName,
                    $ringkasan['kasir']            ?? '',
                    $ringkasan['jumlah_transaksi'] ?? 0,
                    $ringkasan['item_terjual']     ?? 0,
                    $ringkasan['total_diskon']     ?? 0,
                    $ringkasan['total_penjualan']  ?? 0,
                ]]
            ]);

            if ($targetRow) {
                $service->spreadsheets_values->update(
                    $spreadsheetId,
                    $tabRingkasan . '!A' . $targetRow,
                    $ringkasanRow,
                    ['valueInputOption' => 'USER_ENTERED']
                );
            } else {
                $this->appendRows($service, $spreadsheetId, $tabRingkasan, $ringkasanRow->getValues());
            }

            // ════════════════════════════════════════════
            // TAB 2 — "Detail Transaksi - {Cabang}"
            // ════════════════════════════════════════════
            $this->ensureSheetHeader($service, $spreadsheetId, $tabDetail, [
                'Tanggal',
                'Cabang',
                'No. Transaksi',
                'Kasir',
                'Metode Bayar',
                'Nama Item',
                'Custom?',
                'Qty',
                'Harga Satuan',
                'Subtotal Item',
                'Diskon Transaksi',
                'Total Transaksi',
                'Bahan Dikurangi'
            ]);

            $detailRows      = $req->input('detail_transaksi', []);
            $detailFormatted = array_map(fn($row) => [
                $row['tanggal']          ?? '',
                $cabangName,
                $row['no_transaksi']     ?? '',
                $row['kasir']            ?? '',
                $row['metode_bayar']     ?? '',
                $row['nama_item']        ?? '',
                $row['is_custom']        ?? 'Tidak',
                $row['qty']              ?? 0,
                $row['harga_satuan']     ?? 0,
                $row['subtotal_item']    ?? 0,
                $row['diskon_transaksi'] ?? 0,
                $row['total_transaksi']  ?? 0,
                $row['bahan_dikurangi']  ?? '—',
            ], $detailRows);

            if (!empty($detailFormatted)) {
                $this->appendRows($service, $spreadsheetId, $tabDetail, $detailFormatted);
            }

            // ════════════════════════════════════════════
            // TAB 3 — "Mutasi Stok - {Cabang}"
            // ════════════════════════════════════════════
            $this->ensureSheetHeader($service, $spreadsheetId, $tabMutasi, [
                'Tanggal',
                'Cabang',
                'Nama Bahan',
                'Stok Awal',
                'Total Dikurangi Hari Ini',
                'Stok Akhir'
            ]);

            $mutasiRows      = $req->input('mutasi_stok', []);
            $mutasiFormatted = array_map(fn($row) => [
                $row['tanggal']         ?? '',
                $cabangName,
                $row['nama_bahan']      ?? '',
                $row['stok_awal']       ?? 0,
                $row['total_dikurangi'] ?? 0,
                $row['stok_akhir']      ?? 0,
            ], $mutasiRows);

            if (!empty($mutasiFormatted)) {
                $this->appendRows($service, $spreadsheetId, $tabMutasi, $mutasiFormatted);
            }

            return response()->json([
                'success' => true,
                'message' => "Data berhasil dikirim ke 3 tab Google Sheets ({$cabangName})!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function createSpreadsheet(Request $req)
    {
        try {
            $keyPath = storage_path('app/google-service-account.json');

            if (!file_exists($keyPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File service account tidak ditemukan.'
                ], 500);
            }

            $client = new \Google\Client();
            $client->setAuthConfig($keyPath);
            $client->addScope(\Google\Service\Sheets::SPREADSHEETS);
            $client->addScope(\Google\Service\Drive::DRIVE);

            $sheetsService = new \Google\Service\Sheets($client);
            $driveService  = new \Google\Service\Drive($client);

            $user  = Auth::user();
            $tahun = now()->format('Y');
            $title = "Laporan POS - {$tahun}";

            $spreadsheet = new \Google\Service\Sheets\Spreadsheet([
                'properties' => ['title' => $title]
            ]);

            $created = $sheetsService->spreadsheets->create($spreadsheet);
            $newId   = $created->getSpreadsheetId();

            // Share ke email user yang login supaya bisa dibuka di browser
            $shareEmail = $req->input('share_to_email') ?: $user->email;
            if ($shareEmail) {
                $permission = new \Google\Service\Drive\Permission([
                    'type'         => 'user',
                    'role'         => 'writer',
                    'emailAddress' => $shareEmail,
                ]);
                $driveService->permissions->create($newId, $permission, [
                    'sendNotificationEmail' => false,
                ]);
            }

            return response()->json([
                'success'        => true,
                'spreadsheet_id' => $newId,
                'url'            => "https://docs.google.com/spreadsheets/d/{$newId}/edit",
                'message'        => "Spreadsheet \"{$title}\" berhasil dibuat dan dibagikan ke {$shareEmail}.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function ensureSheetExists(\Google\Service\Sheets $service, string $spreadsheetId, string $tab): void
    {
        $spreadsheet    = $service->spreadsheets->get($spreadsheetId);
        $existingSheets = collect($spreadsheet->getSheets())
            ->map(fn($s) => $s->getProperties()->getTitle())
            ->toArray();

        if (!in_array($tab, $existingSheets)) {
            $requests = [
                new \Google\Service\Sheets\Request([
                    'addSheet' => [
                        'properties' => ['title' => $tab]
                    ]
                ])
            ];

            $batchUpdate = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
                'requests' => $requests
            ]);

            $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdate);
        }
    }

    private function ensureSheetHeader(\Google\Service\Sheets $service, string $spreadsheetId, string $tab, array $headers): void
    {
        // Auto-create tab kalau belum ada
        $this->ensureSheetExists($service, $spreadsheetId, $tab);

        try {
            $existing = $service->spreadsheets_values->get($spreadsheetId, $tab . '!A1:A1');
            $rows     = $existing->getValues();
        } catch (\Exception $e) {
            $rows = [];
        }

        if (empty($rows)) {
            $headerRange = new \Google\Service\Sheets\ValueRange([
                'values' => [$headers]
            ]);
            $service->spreadsheets_values->update(
                $spreadsheetId,
                $tab . '!A1',
                $headerRange,
                ['valueInputOption' => 'RAW']
            );
        }
    }

    private function appendRows(\Google\Service\Sheets $service, string $spreadsheetId, string $tab, array $rows): void
    {
        $body = new \Google\Service\Sheets\ValueRange(['values' => $rows]);
        $service->spreadsheets_values->append(
            $spreadsheetId,
            $tab . '!A:Z',
            $body,
            ['valueInputOption' => 'USER_ENTERED', 'insertDataOption' => 'INSERT_ROWS']
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
