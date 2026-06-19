<?php

namespace App\Http\Controllers;

use App\Models\Harga;
use App\Models\Menu;
use App\Models\MutasiStok;
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
        $user = Auth::user();
        $cabangId = $user?->cabang_id;

        $menus = Menu::with([
            'menuDetails.pcsTahu',
            'hargas',
        ])
            ->where('is_active', 1)
            ->orderBy('namaMenu')
            ->get();
        $paymentMethods = ['cash', 'qris', 'gofood', 'shopeefood'];

        $stocksQuery = StokPcs::with('pcsTahu');
        if ($cabangId) {
            $stocksQuery->where('id_cabang', $cabangId);
        }

        $stocks = $stocksQuery->get()->keyBy('id_pcs_tahu');

        $receipt = null;
        if ($request->session()->has('receipt_id')) {
            $receipt = Transaction::with('items')->find($request->session()->get('receipt_id'));
        }

        $today = \Carbon\Carbon::today(config('app.timezone'));

        $todayTransactions = Transaction::whereDate('created_at', $today)->count();
        $todaySales        = Transaction::whereDate('created_at', $today)->sum('total');
        $todayItems        = TransactionItem::whereHas(
            'transaction',
            fn($q) => $q->whereDate('created_at', $today)
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

        $user = Auth::user();
        if (! $user || $user->role !== 'kasir') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Silakan login sebagai kasir.',
            ], 403);
        }

        $cart = json_decode($data['cart'], true);
        if (! is_array($cart) || count($cart) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong.',
            ], 422);
        }

        try {
            $transaction = DB::transaction(function () use ($cart, $data, $user) {
                $subTotal = 0;
                $items = [];
                $stockUsage = [];
                $cabangId = $user->cabang_id ?? null;

                foreach ($cart as $row) {
                    $qty = (int) ($row['qty'] ?? 0);
                    if ($qty <= 0) {
                        throw new \Exception('Jumlah item tidak valid.');
                    }

                    $isCustom = ! empty($row['custom']) || ! empty($row['is_custom']);
                    $menuId = $row['menuId'] ?? $row['menu_id'] ?? null;

                    if ($isCustom || ! $menuId) {
                        $unitPrice = (float) ($row['unitPrice'] ?? $row['unit_price'] ?? 0);
                        $name = trim((string) ($row['name'] ?? $row['nama_item'] ?? 'Custom menu'));

                        if ($unitPrice < 0) {
                            throw new \Exception('Harga custom menu tidak valid.');
                        }

                        $subtotal = $unitPrice * $qty;
                        $subTotal += $subtotal;

                        $items[] = [
                            'menu_id' => null,
                            'nama_item' => $name !== '' ? $name : 'Custom menu',
                            'qty' => $qty,
                            'unit_price' => $unitPrice,
                            'subtotal' => $subtotal,
                            'is_custom' => true,
                        ];

                        continue;
                    }

                    $menu = Menu::with(['menuDetails.pcsTahu', 'hargas'])->find($menuId);
                    if (! $menu) {
                        throw new \Exception('Menu tidak ditemukan.');
                    }

                    $unitPrice = $this->resolveMenuPrice($menu, $data['payment_method'], $row);
                    if ($unitPrice <= 0) {
                        throw new \Exception('Harga untuk menu "' . $menu->namaMenu . '" belum diisi.');
                    }

                    $subtotal = $unitPrice * $qty;
                    $subTotal += $subtotal;

                    $items[] = [
                        'menu_id' => $menu->idMenu,
                        'nama_item' => $menu->namaMenu,
                        'qty' => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                        'is_custom' => false,
                    ];

                    foreach ($menu->menuDetails as $detail) {
                        $pcsId = (int) $detail->id_pcs;
                        $jumlahPcs = (int) $detail->jumlah_pcs;
                        $totalPakai = $jumlahPcs * $qty;

                        if ($pcsId <= 0 || $totalPakai <= 0) {
                            continue;
                        }

                        if (! isset($stockUsage[$pcsId])) {
                            $stockUsage[$pcsId] = [
                                'pcs_id' => $pcsId,
                                'nama_pcs' => $detail->pcsTahu?->nama_pcs ?? 'Bahan ID ' . $pcsId,
                                'jumlah' => 0,
                            ];
                        }

                        $stockUsage[$pcsId]['jumlah'] += $totalPakai;
                    }
                }

                if (count($items) === 0) {
                    throw new \Exception('Tidak ada item valid yang dapat disimpan.');
                }

                foreach ($stockUsage as $usage) {
                    $stok = $this->findStockForCheckout((int) $usage['pcs_id'], $cabangId);

                    if (! $stok) {
                        throw new \Exception('Stok ' . $usage['nama_pcs'] . ' belum tersedia di inventori cabang ini.');
                    }

                    if ((int) $stok->jumlah_stok < (int) $usage['jumlah']) {
                        throw new \Exception(
                            'Stok ' . $usage['nama_pcs'] . ' tidak cukup. Sisa ' .
                                (int) $stok->jumlah_stok . ' pcs, dibutuhkan ' . (int) $usage['jumlah'] . ' pcs.'
                        );
                    }
                }

                $discount = (float) ($data['discount'] ?? 0);
                $total = max(0, $subTotal - $discount);

                $transaction = Transaction::create([
                    'user_id' => $this->getActiveCashierUserId(),
                    'payment_method' => $data['payment_method'],
                    'discount' => $discount,
                    'sub_total' => $subTotal,
                    'total' => $total,
                    'status' => 'completed',
                ]);

                foreach ($items as $itemData) {
                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'menu_id' => $itemData['menu_id'],
                        'nama_item' => $itemData['nama_item'],
                        'qty' => $itemData['qty'],
                        'unit_price' => $itemData['unit_price'],
                        'subtotal' => $itemData['subtotal'],
                        'is_custom' => $itemData['is_custom'],
                    ]);
                }

                foreach ($stockUsage as $usage) {
                    $stok = $this->findStockForCheckout((int) $usage['pcs_id'], $cabangId, true);

                    $stokSebelum = (int) $stok->jumlah_stok;
                    $stokSesudah = $stokSebelum - (int) $usage['jumlah'];

                    $stok->update([
                        'jumlah_stok' => $stokSesudah,
                    ]);

                    if (class_exists(MutasiStok::class)) {
                        MutasiStok::create([
                            'id_cabang' => $stok->id_cabang,
                            'id_pcs_tahu' => $usage['pcs_id'],
                            'tipe' => 'keluar',
                            'jumlah' => (int) $usage['jumlah'],
                            'stok_sebelum' => $stokSebelum,
                            'stok_sesudah' => $stokSesudah,
                            'keterangan' => 'Pengurangan stok dari transaksi #' . $transaction->id,
                        ]);
                    }
                }

                return $transaction;
            });

            session()->flash('receipt_id', $transaction->id);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan dan stok berhasil dikurangi.',
                'id' => $transaction->id,
            ]);
        } catch (\Throwable $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage(),
            ], 422);
        }
    }

    private function resolveMenuPrice(Menu $menu, string $paymentMethod, array $row): float
    {
        $harga = $menu->hargas->first();

        if ($harga) {
            if (in_array($paymentMethod, ['cash', 'qris', 'normal', 'take_away_cash', 'take_away_qris'], true)) {
                if (isset($harga->harga_normal) && $harga->harga_normal !== null) {
                    return (float) $harga->harga_normal;
                }
            }

            if ($paymentMethod === 'gofood' && isset($harga->harga_gofood) && $harga->harga_gofood !== null) {
                return (float) $harga->harga_gofood;
            }

            if ($paymentMethod === 'shopeefood' && isset($harga->harga_shopeefood) && $harga->harga_shopeefood !== null) {
                return (float) $harga->harga_shopeefood;
            }
        }

        return (float) ($row['unitPrice'] ?? $row['unit_price'] ?? 0);
    }

    private function findStockForCheckout(int $pcsId, ?int $cabangId = null, bool $lock = false): ?StokPcs
    {
        $query = StokPcs::where('id_pcs_tahu', $pcsId);

        if ($cabangId) {
            $query->where('id_cabang', $cabangId);
        }

        if ($lock) {
            $query->lockForUpdate();
        }

        $stok = $query->first();

        if (! $stok && ! $cabangId) {
            $fallback = StokPcs::where('id_pcs_tahu', $pcsId)->orderBy('idStokPcs');
            if ($lock) {
                $fallback->lockForUpdate();
            }
            $stok = $fallback->first();
        }

        return $stok;
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
