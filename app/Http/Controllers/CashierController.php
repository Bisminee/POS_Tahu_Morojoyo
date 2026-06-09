<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
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
        $request->session()->forget([
            'selected_shift_id',
            'selected_cashier_name',
            'active_attendance_id',
            'active_karyawan_id',
            'active_karyawan_name',
        ]);

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
            return Shift::with('karyawan')->find((int) $shiftId);
        }
        return null;
    }

    protected function setSelectedShift(Shift $shift): void
    {
        session()->put('selected_shift_id', $shift->id);
        session()->put('selected_cashier_name', $shift->karyawan?->nama ?? 'Kasir');
        session()->save();
    }

    protected function getActiveCashierUserId(): int
    {
        return Auth::id();
    }

    // Helper: ambil id_cabang dari user yang login
    // users.cabang_id → ini yang dipakai di users table
    protected function getIdCabang(): ?int
    {
        return Auth::user()->cabang_id ?? null;
    }

    public function pos(Request $request)
    {
        $paymentMethods = ['cash', 'qris', 'gofood', 'shopeefood'];
        $receipt = null;

        if ($request->session()->has('receipt_id')) {
            $receipt = Transaction::with('items')->find($request->session()->get('receipt_id'));
        }

        $today    = \Carbon\Carbon::today(config('app.timezone'));
        $idCabang = $this->getIdCabang(); // dari users.cabang_id

        // Menu GLOBAL (tidak ada id_cabang di menus)
        $menus = Menu::with(['menuDetails.pcsTahu', 'hargas'])->get();

        // Stok per cabang — stok_pcs pakai id_cabang
        $stocks = StokPcs::with('pcsTahu')
            ->where('id_cabang', $idCabang)
            ->get()
            ->keyBy(fn($s) => $s->pcsTahu?->id_pcs ?? $s->id_pcs_tahu);

        // Statistik hari ini per cabang — transactions pakai id_cabang
        $todayTransactions = Transaction::whereDate('created_at', $today)
            ->where('id_cabang', $idCabang)
            ->count();

        $todaySales = Transaction::whereDate('created_at', $today)
            ->where('id_cabang', $idCabang)
            ->sum('total');

        $todayItems = TransactionItem::whereHas(
            'transaction',
            fn($q) => $q->whereDate('created_at', $today)
                ->where('id_cabang', $idCabang)
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
            'shift'       => $shift,
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
        if (!$this->getActiveAttendance()) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi ditolak. Kasir belum absen masuk menggunakan Face ID.',
            ], 403);
        }

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

        $idCabang     = $this->getIdCabang(); // dari users.cabang_id
        $subTotal     = 0;
        $items        = [];
        $missingStock = [];

        foreach ($cart as $row) {
            $quantity = intval($row['qty'] ?? 0);
            if ($quantity <= 0) continue;

            $isCustom = !empty($row['custom']);

            // MENU CUSTOM
            if ($isCustom) {
                $unitPrice = floatval($row['unitPrice'] ?? $row['unit_price'] ?? 0);
                $subtotal  = $unitPrice * $quantity;

                $items[] = [
                    'menu_id'    => null,
                    'nama_item'  => trim($row['name'] ?? 'Custom menu'),
                    'qty'        => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal'   => $subtotal,
                    'is_custom'  => true,
                ];

                $subTotal += $subtotal;
                continue;
            }

            $menuId = $row['menuId'] ?? $row['menu_id'] ?? null;
            if (!$menuId) continue;

            $menu = Menu::with(['menuDetails.pcsTahu', 'hargas'])->find($menuId);
            if (!$menu) continue;

            $hargaModel = $menu->hargas->first();
            if (!$hargaModel) continue;

            $unitPrice = match ($data['payment_method']) {
                'gofood'     => floatval($hargaModel->harga_gofood ?? 0),
                'shopeefood' => floatval($hargaModel->harga_shopeefood ?? 0),
                default      => floatval($hargaModel->harga_normal ?? 0),
            };

            $subtotal = $unitPrice * $quantity;

            $items[] = [
                'menu_id'    => $menu->idMenu,
                'nama_item'  => $menu->namaMenu,
                'qty'        => $quantity,
                'unit_price' => $unitPrice,
                'subtotal'   => $subtotal,
                'is_custom'  => false,
            ];

            // CEK STOK — stok_pcs pakai id_cabang
            foreach ($menu->menuDetails as $detail) {
                $need = intval($detail->jumlah_pcs) * $quantity;
                $stok = StokPcs::where('id_pcs_tahu', $detail->id_pcs)
                    ->where('id_cabang', $idCabang)
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
            // transactions pakai id_cabang
            $transaction = Transaction::create([
                'user_id'        => $this->getActiveCashierUserId(),
                'id_cabang'      => $idCabang,
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

            // KURANGI STOK — stok_pcs pakai id_cabang
            foreach ($items as $itemData) {
                if ($itemData['is_custom'] || !$itemData['menu_id']) continue;

                $menu = Menu::with('menuDetails')->find($itemData['menu_id']);
                foreach ($menu->menuDetails as $detail) {
                    $need = intval($detail->jumlah_pcs) * $itemData['qty'];
                    $stok = StokPcs::where('id_pcs_tahu', $detail->id_pcs)
                        ->where('id_cabang', $idCabang)
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

    public function syncToSheets(Request $req)
    {
        if (Auth::user()->role !== 'owner') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya owner yang dapat sync ke Google Sheets.'
            ], 403);
        }

        try {
            $keyPath = storage_path('app/google-service-account.json');
            if (!file_exists($keyPath)) {
                return response()->json(['success' => false, 'message' => 'File service account tidak ditemukan.'], 500);
            }

            $spreadsheetId = $req->input('spreadsheet_id');
            if (!$spreadsheetId) {
                return response()->json(['success' => false, 'message' => 'Spreadsheet ID tidak boleh kosong.'], 400);
            }

            $periode = $req->input('periode', 'harian');
            $tz      = config('app.timezone');
            $now     = \Carbon\Carbon::now($tz);

            switch ($periode) {
                case 'mingguan':
                    $startDate    = $now->copy()->startOfWeek();
                    $endDate      = $now->copy()->endOfWeek();
                    $periodeLabel = 'Minggu ' . $now->weekOfYear . ' (' . $startDate->format('d M') . ' – ' . $endDate->format('d M Y') . ')';
                    break;
                case 'bulanan':
                    $startDate    = $now->copy()->startOfMonth();
                    $endDate      = $now->copy()->endOfMonth();
                    $periodeLabel = $now->translatedFormat('F Y');
                    break;
                default:
                    $startDate    = $now->copy()->startOfDay();
                    $endDate      = $now->copy()->endOfDay();
                    $periodeLabel = $now->translatedFormat('d F Y');
                    break;
            }

            // syncToSheets dipanggil owner, owner juga punya cabang_id di users
            $idCabang = $this->getIdCabang(); // users.cabang_id

            $transactions = Transaction::with('items')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('id_cabang', $idCabang) // transactions.id_cabang
                ->get();

            $client = new \Google\Client();
            $client->setAuthConfig($keyPath);
            $client->addScope(\Google\Service\Sheets::SPREADSHEETS);
            $service = new \Google\Service\Sheets($client);

            $user       = Auth::user();
            $cabangName = $user->cabang?->namaCabang ?? 'Semua Cabang';

            $tabRingkasan = "Ringkasan - {$cabangName}";
            $tabDetail    = "Detail Transaksi - {$cabangName}";
            $tabMutasi    = "Mutasi Stok - {$cabangName}";

            // TAB 1 — Ringkasan
            $this->ensureSheetHeader($service, $spreadsheetId, $tabRingkasan, [
                'Periode',
                'Label Periode',
                'Cabang',
                'Jumlah Transaksi',
                'Item Terjual',
                'Total Diskon',
                'Total Penjualan'
            ]);

            $totalTrx   = $transactions->count();
            $totalItems = TransactionItem::whereIn('transaction_id', $transactions->pluck('id'))->sum('qty');
            $totalDisc  = $transactions->sum('discount');
            $totalSales = $transactions->sum('total');

            $existing     = $service->spreadsheets_values->get($spreadsheetId, $tabRingkasan . '!A:B');
            $existingRows = $existing->getValues() ?? [];
            $targetRow    = null;

            foreach ($existingRows as $idx => $row) {
                if (($row[0] ?? '') === $periode && ($row[1] ?? '') === $periodeLabel && $idx > 0) {
                    $targetRow = $idx + 1;
                    break;
                }
            }

            $ringkasanRow = new \Google\Service\Sheets\ValueRange([
                'values' => [[
                    $periode,
                    $periodeLabel,
                    $cabangName,
                    $totalTrx,
                    $totalItems,
                    $totalDisc,
                    $totalSales
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

            // TAB 2 — Detail Transaksi
            $this->ensureSheetHeader($service, $spreadsheetId, $tabDetail, [
                'Periode',
                'Tanggal',
                'No. Transaksi',
                'Kasir',
                'Metode Bayar',
                'Nama Item',
                'Custom?',
                'Qty',
                'Harga Satuan',
                'Subtotal Item',
                'Diskon Transaksi',
                'Total Transaksi'
            ]);

            $methodLabels = [
                'cash'       => 'Tunai',
                'qris'       => 'QRIS',
                'gofood'     => 'GoFood',
                'shopeefood' => 'ShopeeFood',
                'normal'     => 'Tunai/QRIS',
            ];

            $detailRows = [];
            foreach ($transactions as $trx) {
                foreach ($trx->items as $item) {
                    $detailRows[] = [
                        $periodeLabel,
                        $trx->created_at->format('d/m/Y H:i'),
                        'TRX-' . str_pad($trx->id, 5, '0', STR_PAD_LEFT),
                        optional($trx->user)->name ?? '—',
                        $methodLabels[$trx->payment_method] ?? $trx->payment_method,
                        $item->nama_item,
                        $item->is_custom ? 'Ya' : 'Tidak',
                        $item->qty,
                        $item->unit_price,
                        $item->subtotal,
                        $trx->discount,
                        $trx->total,
                    ];
                }
            }

            if (!empty($detailRows)) {
                $this->appendRows($service, $spreadsheetId, $tabDetail, $detailRows);
            }

            // TAB 3 — Mutasi Stok
            $this->ensureSheetHeader($service, $spreadsheetId, $tabMutasi, [
                'Periode',
                'Nama Bahan',
                'Total Dikurangi',
                'Stok Saat Ini'
            ]);

            $stokMutasi = [];
            foreach ($transactions as $trx) {
                foreach ($trx->items as $item) {
                    if ($item->is_custom || !$item->menu_id) continue;
                    $menu = Menu::with('menuDetails.pcsTahu')->find($item->menu_id);
                    if (!$menu) continue;
                    foreach ($menu->menuDetails as $detail) {
                        $nama = $detail->pcsTahu?->nama_pcs ?? 'Bahan';
                        if (!isset($stokMutasi[$nama])) $stokMutasi[$nama] = 0;
                        $stokMutasi[$nama] += $detail->jumlah_pcs * $item->qty;
                    }
                }
            }

            // stok_pcs pakai id_cabang
            $stokSaatIni = StokPcs::with('pcsTahu')
                ->where('id_cabang', $idCabang)
                ->get()
                ->keyBy(fn($s) => $s->pcsTahu?->nama_pcs ?? '—');

            $mutasiRows = [];
            foreach ($stokMutasi as $nama => $totalKurang) {
                $mutasiRows[] = [
                    $periodeLabel,
                    $nama,
                    $totalKurang,
                    $stokSaatIni[$nama]?->jumlah_stok ?? 0,
                ];
            }

            if (!empty($mutasiRows)) {
                $this->appendRows($service, $spreadsheetId, $tabMutasi, $mutasiRows);
            }

            return response()->json([
                'success' => true,
                'message' => "Data {$periodeLabel} berhasil dikirim ke Google Sheets!"
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Laporan Keuangan Realtime per Cabang
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function laporanKeuangan(Request $request)
    {
        $user = auth()->user();

        // Validasi: hanya owner yang bisa akses
        if ($user->role !== 'owner') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya owner yang bisa mengakses laporan keuangan.'
            ], 403);
        }

        try {
            $periode = $request->input('periode', 'harian');

            // Validasi periode
            if (!in_array($periode, ['harian', 'mingguan', 'bulanan'])) {
                $periode = 'harian';
            }

            // Ambil range tanggal berdasarkan periode
            $dateRange = $this->getDateRangeByPeriode($periode);
            $startDate = $dateRange['startDate'];
            $endDate = $dateRange['endDate'];

            // Ambil semua cabang milik owner
            // Asumsi: ada relasi $user->cabangs atau kamu ambil dari table cabangs
            // Jika owner punya multiple cabang, gunakan ini:
            $cabangs = \App\Models\Cabang::where('user_id', $user->id)->get();

            // Atau jika owner cuma punya satu cabang via user.cabang_id:
            // $cabangs = collect([['id_cabang' => $user->cabang_id, 'nama_cabang' => $user->cabang?->nama_cabang ?? 'Cabang']]);

            if ($cabangs->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'periode' => $periode,
                    'summary' => [
                        'total_penjualan' => 0,
                        'total_diskon' => 0,
                        'jumlah_transaksi' => 0,
                        'item_terjual' => 0,
                    ],
                    'cabang_list' => []
                ]);
            }

            // Build laporan per cabang
            $cabangList = [];
            $overallTotal = [
                'total_penjualan' => 0,
                'total_diskon' => 0,
                'jumlah_transaksi' => 0,
                'item_terjual' => 0,
            ];

            foreach ($cabangs as $cabang) {
                // Query transaksi untuk cabang ini dalam periode
                $transactions = Transaction::where('id_cabang', $cabang->id_cabang)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->with('items')
                    ->get();

                // Hitung breakdown metode pembayaran
                $paymentBreakdown = [
                    'cash' => 0,
                    'qris' => 0,
                    'gofood' => 0,
                    'shopeefood' => 0,
                ];

                $totalPenjualan = 0;
                $totalDiskon = 0;
                $itemTerjual = 0;

                foreach ($transactions as $trx) {
                    $totalPenjualan += $trx->total ?? 0;
                    $totalDiskon += $trx->discount ?? 0;

                    // Breakdown per metode pembayaran
                    $method = $this->normalizePaymentMethod($trx->payment_method);
                    if (isset($paymentBreakdown[$method])) {
                        $paymentBreakdown[$method] += $trx->total ?? 0;
                    }

                    // Hitung total items terjual
                    if ($trx->items) {
                        $itemTerjual += $trx->items->sum('qty');
                    }
                }

                // Tambah ke array
                $cabangList[] = [
                    'id_cabang' => $cabang->id_cabang,
                    'nama_cabang' => $cabang->nama_cabang ?? 'Cabang ' . $cabang->id_cabang,
                    'total_penjualan' => (int) $totalPenjualan,
                    'total_diskon' => (int) $totalDiskon,
                    'jumlah_transaksi' => $transactions->count(),
                    'item_terjual' => (int) $itemTerjual,
                    'metode_pembayaran' => [
                        'cash' => (int) $paymentBreakdown['cash'],
                        'qris' => (int) $paymentBreakdown['qris'],
                        'gofood' => (int) $paymentBreakdown['gofood'],
                        'shopeefood' => (int) $paymentBreakdown['shopeefood'],
                    ],
                ];

                // Accumulate overall totals
                $overallTotal['total_penjualan'] += $totalPenjualan;
                $overallTotal['total_diskon'] += $totalDiskon;
                $overallTotal['jumlah_transaksi'] += $transactions->count();
                $overallTotal['item_terjual'] += $itemTerjual;
            }

            return response()->json([
                'success' => true,
                'periode' => $periode,
                'summary' => $overallTotal,
                'cabang_list' => $cabangList,
            ]);
        } catch (\Exception $e) {
            \Log::error('Laporan Keuangan Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Ambil range tanggal berdasarkan periode
     * 
     * @param string $periode
     * @return array
     */
    private function getDateRangeByPeriode($periode)
    {
        $today = now();

        switch ($periode) {
            case 'harian':
                return [
                    'startDate' => $today->clone()->startOfDay(),
                    'endDate' => $today->clone()->endOfDay(),
                ];

            case 'mingguan':
                return [
                    'startDate' => $today->clone()->startOfWeek(),
                    'endDate' => $today->clone()->endOfWeek(),
                ];

            case 'bulanan':
                return [
                    'startDate' => $today->clone()->startOfMonth(),
                    'endDate' => $today->clone()->endOfMonth(),
                ];

            default:
                return [
                    'startDate' => $today->clone()->startOfDay(),
                    'endDate' => $today->clone()->endOfDay(),
                ];
        }
    }

    /**
     * Helper: Normalize payment method string
     * 
     * @param string $method
     * @return string
     */
    private function normalizePaymentMethod($method)
    {
        $m = strtolower(trim($method ?? ''));

        // Cash
        if (in_array($m, ['tunai', 'cash'])) {
            return 'cash';
        }

        // QRIS
        if (in_array($m, ['qris'])) {
            return 'qris';
        }

        // GoFood
        if (in_array($m, ['go food', 'gofood'])) {
            return 'gofood';
        }

        // ShopeeFood
        if (in_array($m, ['shopee food', 'shopeefood'])) {
            return 'shopeefood';
        }

        return $m;
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
                    'addSheet' => ['properties' => ['title' => $tab]]
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
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
