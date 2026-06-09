<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Menu;
use App\Models\StokPcs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerSheetsController extends Controller
{
    /**
     * Tampilkan halaman sync Google Sheets untuk owner.
     */
    public function index()
    {
        $this->authorizeOwner();

        $today   = \Carbon\Carbon::today(config('app.timezone'));
        $cabangs = Cabang::orderBy('namaCabang')->get();

        $shiftBoundary = $today->copy()->setTime(15, 0);

        // Stats per cabang (hari ini)
        $statsPerCabang = $cabangs->map(function ($cabang) use ($today, $shiftBoundary) {
            // Ambil semua user kasir di cabang ini
            $userIds = User::where('cabang_id', $cabang->idCabang)->pluck('id');

            $trx = Transaction::whereIn('user_id', $userIds)
                ->whereDate('created_at', $today)
                ->get();

            $shift1Sales = $trx->filter(fn($t) => $t->created_at->lte($shiftBoundary))->sum('total');
            $shift2Sales = $trx->filter(fn($t) => $t->created_at->gt($shiftBoundary))->sum('total');

            return [
                'id'           => $cabang->idCabang,
                'nama'         => $cabang->namaCabang,
                'trx'          => $trx->count(),
                'sales'        => $trx->sum('total'),
                'shift1_sales' => $shift1Sales,
                'shift2_sales' => $shift2Sales,
                'items'        => TransactionItem::whereIn('transaction_id', $trx->pluck('id'))->sum('qty'),
            ];
        });

        // Stats total semua cabang (hari ini)
        $statsAll = [
            'today_trx'   => Transaction::whereDate('created_at', $today)->count(),
            'today_sales' => Transaction::whereDate('created_at', $today)->sum('total'),
            'today_items' => TransactionItem::whereHas(
                'transaction',
                fn($q) => $q->whereDate('created_at', $today)
            )->sum('qty'),
            'shift1_sales' => Transaction::whereDate('created_at', $today)
                ->whereTime('created_at', '<=', $shiftBoundary->format('H:i:s'))
                ->sum('total'),
            'shift2_sales' => Transaction::whereDate('created_at', $today)
                ->whereTime('created_at', '>', $shiftBoundary->format('H:i:s'))
                ->sum('total'),
        ];

        return view('owner.sheets.index', compact('statsAll', 'statsPerCabang', 'cabangs'));
    }

    public function create()
    {
        return view('owner.sheets.create');
    }


    public function createSpreadsheet(Request $request)
    {
        $client = $this->makeGoogleClient([
            \Google\Service\Sheets::SPREADSHEETS,
            \Google\Service\Drive::DRIVE,
        ]);

        $drive = new \Google\Service\Drive($client);

        try {

            $sheetsService = new \Google\Service\Sheets($client);

            $spreadsheet = new \Google\Service\Sheets\Spreadsheet();

            $drive = new \Google\Service\Drive($client);

            $fileMetadata = new \Google\Service\Drive\DriveFile([
                'name' => 'Laporan POS',
                'mimeType' => 'application/vnd.google-apps.spreadsheet'
            ]);

            $file = $drive->files->create($fileMetadata);

            dd(
                'BERHASIL',
                $file->getId()
            );

            dd(
                'SHEETS BERHASIL',
                $created->getSpreadsheetId()
            );
        } catch (\Throwable $e) {

            dd(
                get_class($e),
                $e->getCode(),
                $e->getMessage(),
                method_exists($e, 'getErrors')
                    ? $e->getErrors()
                    : null
            );
        }
    }

    public function sync(Request $request)
    {
        $this->authorizeOwner();

        $request->validate([
            'spreadsheet_id' => ['required', 'string'],
            'periode'        => ['required', 'in:harian,mingguan,bulanan'],
            'cabang_id'      => ['required'],
        ]);

        try {
            $spreadsheetId = $request->input('spreadsheet_id');
            $periode       = $request->input('periode', 'harian');
            $cabangId      = $request->input('cabang_id'); // 'all' atau integer

            // ── Range tanggal ──
            $tz  = config('app.timezone');
            $now = \Carbon\Carbon::now($tz);

            switch ($periode) {
                case 'mingguan':
                    $startDate    = $now->copy()->startOfWeek();
                    $endDate      = $now->copy()->endOfWeek();
                    $periodeLabel = 'Minggu ' . $now->weekOfYear
                        . ' (' . $startDate->format('d M')
                        . ' – ' . $endDate->format('d M Y') . ')';
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

            // ── Tentukan cabang yang akan disync ──
            if ($cabangId === 'all') {
                $cabangs = Cabang::orderBy('namaCabang')->get();
            } else {
                $cabangs = Cabang::where('idCabang', (int) $cabangId)->get();
                if ($cabangs->isEmpty()) {
                    return response()->json(['success' => false, 'message' => 'Cabang tidak ditemukan.'], 404);
                }
            }

            // ── Google Client ──
            $client  = $this->makeGoogleClient([\Google\Service\Sheets::SPREADSHEETS]);
            $service = new \Google\Service\Sheets($client);

            $grandTotalTrx   = 0;
            $grandTotalItems = 0;
            $grandTotalDisc  = 0;
            $grandTotalSales = 0;

            foreach ($cabangs as $cabang) {
                $cabangName = $cabang->namaCabang;

                // User kasir di cabang ini
                $userIds = User::where('cabang_id', $cabang->idCabang)->pluck('id');

                // Transaksi cabang ini dalam periode
                $transactions = Transaction::with(['items', 'user'])
                    ->whereIn('user_id', $userIds)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

                $tabRingkasan = "Ringkasan - {$cabangName}";
                $tabDetail    = "Detail - {$cabangName}";
                $tabMutasi    = "Mutasi Stok - {$cabangName}";

                // ── TAB 1: Ringkasan ──
                $this->ensureSheetHeader($service, $spreadsheetId, $tabRingkasan, [
                    'Periode',
                    'Label Periode',
                    'Cabang',
                    'Jumlah Transaksi',
                    'Item Terjual',
                    'Total Diskon',
                    'Total Penjualan',
                ]);

                $totalTrx   = $transactions->count();
                $totalItems = TransactionItem::whereIn('transaction_id', $transactions->pluck('id'))->sum('qty');
                $totalDisc  = $transactions->sum('discount');
                $totalSales = $transactions->sum('total');

                $grandTotalTrx   += $totalTrx;
                $grandTotalItems += $totalItems;
                $grandTotalDisc  += $totalDisc;
                $grandTotalSales += $totalSales;

                // Cek duplikat — update jika sudah ada, append jika belum
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
                        $totalSales,
                    ]],
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

                // ── TAB 2: Detail Transaksi ──
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
                    'Total Transaksi',
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

                // ── TAB 3: Mutasi Stok ──
                $this->ensureSheetHeader($service, $spreadsheetId, $tabMutasi, [
                    'Periode',
                    'Nama Bahan',
                    'Total Dikurangi',
                    'Stok Saat Ini',
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

                $stokSaatIni = StokPcs::with('pcsTahu')->get()->keyBy(fn($s) => $s->pcsTahu?->nama_pcs ?? '—');
                $mutasiRows  = [];
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
            } // end foreach cabang

            // ── TAB Gabungan jika sync semua cabang ──
            if ($cabangId === 'all' && $cabangs->count() > 1) {
                $tabGabungan = 'Ringkasan Semua Cabang';
                $this->ensureSheetHeader($service, $spreadsheetId, $tabGabungan, [
                    'Periode',
                    'Label Periode',
                    'Cabang',
                    'Jumlah Transaksi',
                    'Item Terjual',
                    'Total Diskon',
                    'Total Penjualan',
                ]);

                // Tulis baris total
                $this->appendRows($service, $spreadsheetId, $tabGabungan, [[
                    $periode,
                    $periodeLabel,
                    'SEMUA CABANG',
                    $grandTotalTrx,
                    $grandTotalItems,
                    $grandTotalDisc,
                    $grandTotalSales,
                ]]);
            }

            $cabangLabel = $cabangId === 'all'
                ? 'Semua Cabang (' . $cabangs->count() . ' cabang)'
                : $cabangs->first()->namaCabang;

            return response()->json([
                'success' => true,
                'message' => "Data {$periodeLabel} — {$cabangLabel} berhasil dikirim ke Google Sheets!",
                'stats'   => [
                    'jumlah_transaksi' => $grandTotalTrx,
                    'item_terjual'     => $grandTotalItems,
                    'total_diskon'     => $grandTotalDisc,
                    'total_penjualan'  => $grandTotalSales,
                    'cabang_label'     => $cabangLabel,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function showLaporanKeuangan()
    {
        $this->authorizeOwner();
        return view('owner.laporan-keuangan');
    }

    public function laporanKeuangan(Request $request)
    {
        $user = auth()->user();

        if ($user->role !== 'owner') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        try {
            $periode = $request->input('periode', 'harian');
            if (!in_array($periode, ['harian', 'mingguan', 'bulanan'])) {
                $periode = 'harian';
            }

            $dateRange = $this->getDateRangeByPeriode($periode);
            $startDate = $dateRange['startDate'];
            $endDate   = $dateRange['endDate'];

            $cabangs = \App\Models\Cabang::orderBy('namaCabang')->get();

            if ($cabangs->isEmpty()) {
                return response()->json([
                    'success'     => true,
                    'periode'     => $periode,
                    'summary'     => ['total_penjualan' => 0, 'total_diskon' => 0, 'jumlah_transaksi' => 0, 'item_terjual' => 0],
                    'cabang_list' => [],
                ]);
            }

            $cabangList   = [];
            $overallTotal = ['total_penjualan' => 0, 'total_diskon' => 0, 'jumlah_transaksi' => 0, 'item_terjual' => 0];

            foreach ($cabangs as $cabang) {
                $userIds = \App\Models\User::where('cabang_id', $cabang->idCabang)->pluck('id');

                $transactions = \App\Models\Transaction::whereIn('user_id', $userIds)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->with('items')
                    ->get();

                $paymentBreakdown = ['cash' => 0, 'qris' => 0, 'gofood' => 0, 'shopeefood' => 0];
                $totalPenjualan   = 0;
                $totalDiskon      = 0;
                $itemTerjual      = 0;

                foreach ($transactions as $trx) {
                    $totalPenjualan += $trx->total ?? 0;
                    $totalDiskon    += $trx->discount ?? 0;

                    $method = $this->normalizePaymentMethod($trx->payment_method);
                    if (isset($paymentBreakdown[$method])) {
                        $paymentBreakdown[$method] += $trx->total ?? 0;
                    }

                    if ($trx->items) {
                        $itemTerjual += $trx->items->sum('qty');
                    }
                }

                $cabangList[] = [
                    'id_cabang'         => $cabang->idCabang,
                    'nama_cabang'       => $cabang->namaCabang,
                    'total_penjualan'   => (int) $totalPenjualan,
                    'total_diskon'      => (int) $totalDiskon,
                    'jumlah_transaksi'  => $transactions->count(),
                    'item_terjual'      => (int) $itemTerjual,
                    'metode_pembayaran' => [
                        'cash'       => (int) $paymentBreakdown['cash'],
                        'qris'       => (int) $paymentBreakdown['qris'],
                        'gofood'     => (int) $paymentBreakdown['gofood'],
                        'shopeefood' => (int) $paymentBreakdown['shopeefood'],
                    ],
                ];

                $overallTotal['total_penjualan']  += $totalPenjualan;
                $overallTotal['total_diskon']     += $totalDiskon;
                $overallTotal['jumlah_transaksi'] += $transactions->count();
                $overallTotal['item_terjual']     += $itemTerjual;
            }

            return response()->json([
                'success'     => true,
                'periode'     => $periode,
                'summary'     => $overallTotal,
                'cabang_list' => $cabangList,
            ]);
        } catch (\Exception $e) {
            \Log::error('Laporan Keuangan Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
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
     * Helper: Normalize payment method
     */
    private function normalizePaymentMethod($method)
    {
        $m = strtolower(trim($method ?? ''));

        if (in_array($m, ['tunai', 'cash'])) {
            return 'cash';
        }
        if (in_array($m, ['qris'])) {
            return 'qris';
        }
        if (in_array($m, ['go food', 'gofood'])) {
            return 'gofood';
        }
        if (in_array($m, ['shopee food', 'shopeefood'])) {
            return 'shopeefood';
        }

        return $m;
    }

    private function authorizeOwner(): void
    {
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Akses hanya untuk Owner.');
        }
    }

    private function makeGoogleClient(array $scopes): \Google\Client
    {
        $keyPath = storage_path('app/google-service-account.json');
        if (!file_exists($keyPath)) {
            throw new \RuntimeException('File service account Google tidak ditemukan di storage/app/google-service-account.json');
        }
        $client = new \Google\Client();
        $client->setAuthConfig($keyPath);
        foreach ($scopes as $scope) {
            $client->addScope($scope);
        }
        return $client;
    }

    private function ensureSheetExists(\Google\Service\Sheets $service, string $spreadsheetId, string $tab): void
    {
        $spreadsheet    = $service->spreadsheets->get($spreadsheetId);
        $existingSheets = collect($spreadsheet->getSheets())
            ->map(fn($s) => $s->getProperties()->getTitle())
            ->toArray();

        if (!in_array($tab, $existingSheets)) {
            $batchUpdate = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
                'requests' => [
                    new \Google\Service\Sheets\Request([
                        'addSheet' => ['properties' => ['title' => $tab]],
                    ]),
                ],
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
            $headerRange = new \Google\Service\Sheets\ValueRange(['values' => [$headers]]);
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
}
