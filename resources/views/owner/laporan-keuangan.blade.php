{{-- resources/views/owner/laporan-keuangan.blade.php --}}

@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
    <div class="lk-wrap">

        {{-- Header --}}
        <div class="lk-header">
            <div>
                <div class="lk-eyebrow">Owner Dashboard</div>
                <h1 class="lk-title">Laporan Keuangan</h1>
                <p class="lk-sub">Pantau penjualan semua cabang secara realtime</p>
            </div>
            <div class="lk-refresh-wrap">
                <button class="lk-btn-refresh" onclick="loadLaporanKeuangan(selectedPeriodeLaporan)" title="Refresh data">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M23 4v6h-6M1 20v-6h6" />
                        <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15" />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>

        {{-- Periode Filter --}}
        <div class="lk-periode-bar">
            <span class="lk-periode-label">Periode:</span>
            <div class="lk-periode-tabs">
                <button class="lk-tab active" id="tab-harian" onclick="setPeriodeLaporan('harian')">Harian</button>
                <button class="lk-tab" id="tab-mingguan" onclick="setPeriodeLaporan('mingguan')">Mingguan</button>
                <button class="lk-tab" id="tab-bulanan" onclick="setPeriodeLaporan('bulanan')">Bulanan</button>
            </div>
            <span class="lk-periode-info" id="periode-info">—</span>
        </div>

        {{-- Summary Cards --}}
        <div class="lk-summary-grid" id="laporan-summary">
            <div class="lk-summary-card lk-summary-sales">
                <div class="lk-summary-body">
                    <div class="lk-summary-label">Total Penjualan</div>
                    <div class="lk-summary-value" id="total-penjualan">Rp0</div>
                </div>
            </div>
            <div class="lk-summary-card lk-summary-trx">
                <div class="lk-summary-body">
                    <div class="lk-summary-label">Total Transaksi</div>
                    <div class="lk-summary-value" id="total-transaksi">0</div>
                </div>
            </div>
            <div class="lk-summary-card lk-summary-item">
                <div class="lk-summary-body">
                    <div class="lk-summary-label">Item Terjual</div>
                    <div class="lk-summary-value" id="total-items">0</div>
                </div>
            </div>
            <div class="lk-summary-card lk-summary-disc">
                <div class="lk-summary-body">
                    <div class="lk-summary-label">Total Diskon</div>
                    <div class="lk-summary-value" id="total-diskon">Rp0</div>
                </div>
            </div>
        </div>

        {{-- Section title --}}
        <div class="lk-section-title">
            <span>Laporan per Cabang</span>
            <span class="lk-cabang-count" id="cabang-count"></span>
        </div>

        {{-- Cabang List --}}
        <div id="laporan-cabang-list">
            <div class="lk-loading">
                <div class="lk-spinner"></div>
                <p>Memuat data laporan...</p>
            </div>
        </div>

    </div>

    <style>
        /* ── Wrap ── */
        .lk-wrap {
            max-width: 960px;
            margin: 0 auto;
            padding: 28px 20px 60px;
            font-family: inherit;
        }

        /* ── Header ── */
        .lk-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        .lk-eyebrow {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #C0271A;
            margin-bottom: 4px;
        }

        .lk-title {
            font-size: 26px;
            font-weight: 800;
            color: #1a1a1a;
            margin: 0 0 4px;
            line-height: 1.2;
        }

        .lk-sub {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
        }

        .lk-btn-refresh {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: border-color .15s, background .15s;
        }

        .lk-btn-refresh:hover {
            border-color: #C0271A;
            color: #C0271A;
            background: #fff5f5;
        }

        /* ── Periode Bar ── */
        .lk-periode-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px 16px;
            margin-bottom: 20px;
        }

        .lk-periode-label {
            font-size: 12px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: .08em;
            white-space: nowrap;
        }

        .lk-periode-tabs {
            display: flex;
            background: #f3f4f6;
            border-radius: 7px;
            padding: 3px;
            gap: 2px;
        }

        .lk-tab {
            padding: 5px 16px;
            border: none;
            border-radius: 5px;
            background: transparent;
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            transition: background .15s, color .15s;
        }

        .lk-tab.active {
            background: #fff;
            color: #C0271A;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
        }

        .lk-tab:hover:not(.active) {
            color: #374151;
        }

        .lk-periode-info {
            margin-left: auto;
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
        }

        /* ── Summary Grid ── */
        .lk-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 28px;
        }

        @media (max-width: 700px) {
            .lk-summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .lk-summary-card {
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1.5px solid transparent;
        }

        .lk-summary-icon {
            font-size: 26px;
            line-height: 1;
        }

        .lk-summary-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            opacity: .7;
            margin-bottom: 3px;
        }

        .lk-summary-value {
            font-size: 20px;
            font-weight: 800;
            line-height: 1.1;
        }

        .lk-summary-sales {
            background: #fff5f5;
            border-color: #fecaca;
            color: #991b1b;
        }

        .lk-summary-trx {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1e40af;
        }

        .lk-summary-item {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }

        .lk-summary-disc {
            background: #fefce8;
            border-color: #fde68a;
            color: #92400e;
        }

        /* ── Section Title ── */
        .lk-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 800;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f3f4f6;
        }

        .lk-cabang-count {
            font-size: 11px;
            background: #f3f4f6;
            color: #6b7280;
            border-radius: 20px;
            padding: 2px 10px;
            font-weight: 700;
        }

        /* ── Cabang Card ── */
        .lk-cabang-card {
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: 14px;
            margin-bottom: 14px;
            overflow: hidden;
            transition: box-shadow .15s;
        }

        .lk-cabang-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, .07);
        }

        .lk-cabang-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1.5px solid #f3f4f6;
        }

        .lk-cabang-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .lk-cabang-avatar {
            width: 40px;
            height: 40px;
            background: #fff5f5;
            border: 1.5px solid #fecaca;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .lk-cabang-name {
            font-size: 16px;
            font-weight: 800;
            color: #1a1a1a;
        }

        .lk-cabang-meta {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 1px;
        }

        .lk-cabang-right {
            text-align: right;
        }

        .lk-cabang-total {
            font-size: 20px;
            font-weight: 800;
            color: #C0271A;
            line-height: 1;
        }

        .lk-cabang-disc {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 3px;
        }

        /* Zero state */
        .lk-cabang-card.lk-zero .lk-cabang-total {
            color: #9ca3af;
        }

        .lk-cabang-card.lk-zero .lk-cabang-top {
            background: #fafafa;
        }

        /* ── Metode Grid ── */
        .lk-metode-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
        }

        @media (max-width: 600px) {
            .lk-metode-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .lk-metode-item {
            padding: 14px 20px;
            border-right: 1.5px solid #f3f4f6;
            position: relative;
        }

        .lk-metode-item:last-child {
            border-right: none;
        }

        .lk-metode-bar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 0;
        }

        .lk-metode-cash .lk-metode-bar {
            background: #22c55e;
        }

        .lk-metode-qris .lk-metode-bar {
            background: #f59e0b;
        }

        .lk-metode-gofood .lk-metode-bar {
            background: #ef4444;
        }

        .lk-metode-shopee .lk-metode-bar {
            background: #f97316;
        }

        .lk-metode-head {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .lk-metode-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .lk-metode-cash .lk-metode-dot {
            background: #22c55e;
        }

        .lk-metode-qris .lk-metode-dot {
            background: #f59e0b;
        }

        .lk-metode-gofood .lk-metode-dot {
            background: #ef4444;
        }

        .lk-metode-shopee .lk-metode-dot {
            background: #f97316;
        }

        .lk-metode-amount {
            font-size: 15px;
            font-weight: 800;
            color: #1a1a1a;
        }

        .lk-metode-pct {
            font-size: 11px;
            color: #9ca3af;
            font-weight: 500;
            margin-top: 2px;
        }

        /* ── Loading ── */
        .lk-loading {
            text-align: center;
            padding: 48px 0;
            color: #9ca3af;
        }

        .lk-spinner {
            width: 32px;
            height: 32px;
            border: 3px solid #e5e7eb;
            border-top-color: #C0271A;
            border-radius: 50%;
            animation: lk-spin .7s linear infinite;
            margin: 0 auto 12px;
        }

        @keyframes lk-spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <script>
        let selectedPeriodeLaporan = 'harian';
        const LAPORAN_URL = @json(route('owner.laporan-keuangan.data'));
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        const PERIODE_LABELS = {
            harian: 'Hari Ini',
            mingguan: 'Minggu Ini',
            bulanan: 'Bulan Ini'
        };

        function setPeriodeLaporan(periode) {
            document.querySelectorAll('.lk-tab').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + periode).classList.add('active');
            selectedPeriodeLaporan = periode;
            loadLaporanKeuangan(periode);
        }

        function formatRupiah(num) {
            return 'Rp' + Number(num || 0).toLocaleString('id-ID', {
                maximumFractionDigits: 0
            });
        }

        function pct(part, total) {
            if (!total) return '0%';
            return Math.round((part / total) * 100) + '%';
        }

        async function loadLaporanKeuangan(periode = 'harian') {
            const container = document.getElementById('laporan-cabang-list');
            container.innerHTML =
                `<div class="lk-loading"><div class="lk-spinner"></div><p>Memuat data laporan...</p></div>`;

            try {
                const res = await fetch(LAPORAN_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        periode
                    })
                });

                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const data = await res.json();
                if (!data.success) throw new Error(data.message || 'Gagal memuat laporan');

                // Update periode info
                document.getElementById('periode-info').textContent = PERIODE_LABELS[periode] || periode;

                // Update summary
                document.getElementById('total-penjualan').textContent = formatRupiah(data.summary.total_penjualan);
                document.getElementById('total-transaksi').textContent = data.summary.jumlah_transaksi.toLocaleString(
                    'id-ID');
                document.getElementById('total-items').textContent = data.summary.item_terjual.toLocaleString('id-ID');
                document.getElementById('total-diskon').textContent = formatRupiah(data.summary.total_diskon);

                // Cabang count
                const count = data.cabang_list?.length || 0;
                document.getElementById('cabang-count').textContent = count + ' cabang';

                // Render cabang
                container.innerHTML = '';
                if (!count) {
                    container.innerHTML =
                        `<div style="text-align:center;padding:40px;color:#9ca3af;font-size:14px;">Tidak ada data untuk periode ini</div>`;
                    return;
                }

                data.cabang_list.forEach(cabang => {
                    const m = cabang.metode_pembayaran;
                    const tot = cabang.total_penjualan;
                    const zero = tot === 0;

                    container.innerHTML += `
            <div class="lk-cabang-card${zero ? ' lk-zero' : ''}">
                <div class="lk-cabang-top">
                    <div class="lk-cabang-left">
                        <div class="lk-cabang-avatar">🏪</div>
                        <div>
                            <div class="lk-cabang-name">${cabang.nama_cabang}</div>
                            <div class="lk-cabang-meta">${cabang.jumlah_transaksi.toLocaleString('id-ID')} transaksi &middot; ${cabang.item_terjual.toLocaleString('id-ID')} item terjual</div>
                        </div>
                    </div>
                    <div class="lk-cabang-right">
                        <div class="lk-cabang-total">${formatRupiah(tot)}</div>
                        ${cabang.total_diskon > 0 ? `<div class="lk-cabang-disc">Diskon: ${formatRupiah(cabang.total_diskon)}</div>` : ''}
                    </div>
                </div>
                <div class="lk-metode-grid">
                    <div class="lk-metode-item lk-metode-cash">
                        <div class="lk-metode-bar"></div>
                        <div class="lk-metode-head"><span class="lk-metode-dot"></span>Cash</div>
                        <div class="lk-metode-amount">${formatRupiah(m.cash)}</div>
                        <div class="lk-metode-pct">${pct(m.cash, tot)} dari total</div>
                    </div>
                    <div class="lk-metode-item lk-metode-qris">
                        <div class="lk-metode-bar"></div>
                        <div class="lk-metode-head"><span class="lk-metode-dot"></span>QRIS</div>
                        <div class="lk-metode-amount">${formatRupiah(m.qris)}</div>
                        <div class="lk-metode-pct">${pct(m.qris, tot)} dari total</div>
                    </div>
                    <div class="lk-metode-item lk-metode-gofood">
                        <div class="lk-metode-bar"></div>
                        <div class="lk-metode-head"><span class="lk-metode-dot"></span>GoFood</div>
                        <div class="lk-metode-amount">${formatRupiah(m.gofood)}</div>
                        <div class="lk-metode-pct">${pct(m.gofood, tot)} dari total</div>
                    </div>
                    <div class="lk-metode-item lk-metode-shopee">
                        <div class="lk-metode-bar"></div>
                        <div class="lk-metode-head"><span class="lk-metode-dot"></span>ShopeeFood</div>
                        <div class="lk-metode-amount">${formatRupiah(m.shopeefood)}</div>
                        <div class="lk-metode-pct">${pct(m.shopeefood, tot)} dari total</div>
                    </div>
                </div>
            </div>`;
                });

            } catch (err) {
                console.error(err);
                container.innerHTML =
                    `<div class="alert alert-danger mt-2">❌ ${err.message || 'Terjadi kesalahan saat memuat laporan'}</div>`;
            }
        }

        document.addEventListener('DOMContentLoaded', () => loadLaporanKeuangan('harian'));
    </script>
@endsection
