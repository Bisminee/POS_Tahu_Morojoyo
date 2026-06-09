@props(['title' => 'Sync Google Sheets'])

<x-layouts.app :title="$title">

    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Nunito:wght@400;600;700;800&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            red: '#C0271A',
                            darkred: '#9B1E13',
                            yellow: '#F5C518',
                            cream: '#FFF8E7'
                        }
                    },
                    fontFamily: {
                        display: ['Bebas Neue', 'sans-serif'],
                        body: ['Nunito', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        * {
            box-sizing: border-box
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: #FFF8E7
        }

        .page-wrap {
            max-width: 900px;
            margin: 0 auto;
            padding: 28px 20px;
            display: flex;
            flex-direction: column;
            gap: 20px
        }

        /* TOPBAR */
        .topbar {
            background: #C0271A;
            border-radius: 16px;
            padding: 16px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 4px 16px rgba(192, 39, 26, .25)
        }

        .topbar h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 26px;
            color: #F5C518;
            letter-spacing: .1em
        }

        .topbar p {
            font-size: 12px;
            color: #fecaca;
            margin-top: 2px
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 99px;
            background: rgba(255, 255, 255, .12);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .25);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: background .15s;
            font-family: 'Nunito', sans-serif
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, .22)
        }

        /* CARD */
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 22px 24px;
            border: 2px solid #f0d9a0;
            box-shadow: 0 2px 10px rgba(192, 39, 26, .06)
        }

        .card-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 15px;
            letter-spacing: .12em;
            color: #C0271A;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px
        }

        /* STAT */
        .stat-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px
        }

        .stat-card {
            background: #FFF8E7;
            border-radius: 14px;
            padding: 16px 18px;
            border: 2px solid #f0d9a0
        }

        .stat-card .s-label {
            font-size: 11px;
            color: #9ca3af;
            font-weight: 700
        }

        .stat-card .s-val {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 22px;
            margin-top: 6px;
            letter-spacing: .05em
        }

        .s-green {
            color: #059669
        }

        .s-red {
            color: #C0271A
        }

        .s-amber {
            color: #d97706
        }

        /* CABANG TABLE */
        .cabang-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px
        }

        .cabang-table th {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 12px;
            letter-spacing: .1em;
            color: #C0271A;
            padding: 8px 12px;
            text-align: left;
            border-bottom: 2px solid #f0d9a0;
            background: #FFF8E7
        }

        .cabang-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #fef3c7;
            color: #374151;
            vertical-align: middle
        }

        .cabang-table tr:last-child td {
            border-bottom: none
        }

        .cabang-table tr:hover td {
            background: #FFFDF5
        }

        .cabang-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
            background: #FFF8E7;
            color: #9B1E13;
            border: 1px solid #f0d9a0
        }

        /* CABANG SELECTOR */
        .cabang-selector {
            display: flex;
            flex-wrap: wrap;
            gap: 8px
        }

        .cabang-btn {
            padding: 9px 18px;
            border-radius: 99px;
            border: 2px solid #f0d9a0;
            background: #fff;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            color: #9ca3af;
            transition: all .15s;
            font-family: 'Nunito', sans-serif;
            display: flex;
            align-items: center;
            gap: 6px
        }

        .cabang-btn:hover {
            border-color: #C0271A;
            color: #C0271A
        }

        .cabang-btn.active {
            background: #C0271A;
            border-color: #C0271A;
            color: #F5C518
        }

        .cabang-btn.all-btn.active {
            background: #9B1E13
        }

        /* PERIODE */
        .pay-btn {
            padding: 9px 20px;
            border-radius: 99px;
            border: 2px solid #f0d9a0;
            background: #fff;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            color: #9ca3af;
            transition: all .15s;
            font-family: 'Nunito', sans-serif;
            display: flex;
            align-items: center;
            gap: 6px
        }

        .pay-btn:hover {
            border-color: #C0271A;
            color: #C0271A
        }

        .pay-btn.active {
            background: #C0271A;
            border-color: #C0271A;
            color: #F5C518
        }

        /* INPUT */
        .input-field {
            width: 100%;
            border: 2px solid #f0d9a0;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
            font-family: 'Nunito', sans-serif;
            color: #1f1f1f;
            background: #fff;
            outline: none;
            transition: border-color .15s
        }

        .input-field:focus {
            border-color: #C0271A
        }

        .input-field::placeholder {
            color: #d1d5db
        }

        .input-label {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 13px;
            letter-spacing: .1em;
            color: #C0271A;
            display: block;
            margin-bottom: 7px
        }

        .hint {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 5px
        }

        .hint code {
            background: #f0d9a0;
            padding: 1px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #9B1E13
        }

        /* BUTTONS */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 28px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #C0271A 0%, #9B1E13 100%);
            color: #F5C518;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 18px;
            letter-spacing: .08em;
            cursor: pointer;
            transition: all .15s;
            box-shadow: 0 4px 14px rgba(192, 39, 26, .3);
            width: 100%
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #9B1E13 0%, #7a1710 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(192, 39, 26, .4)
        }

        .btn-primary:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            box-shadow: none;
            transform: none
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 24px;
            border-radius: 12px;
            border: 2px solid #f0d9a0;
            background: #fff;
            color: #374151;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s;
            font-family: 'Nunito', sans-serif;
            width: 100%
        }

        .btn-secondary:hover {
            background: #FFF8E7
        }

        .btn-secondary:disabled {
            opacity: .5;
            cursor: not-allowed
        }

        /* STATUS */
        .sync-status {
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 13px;
            font-weight: 700;
            display: none;
            line-height: 1.6
        }

        .sync-ok {
            background: #f0fdf4;
            color: #166534;
            border: 1.5px solid #bbf7d0
        }

        .sync-err {
            background: #fef2f2;
            color: #991b1b;
            border: 1.5px solid #fecaca
        }

        .sync-wait {
            background: #FFF8E7;
            color: #9B1E13;
            border: 1.5px solid #F5C518
        }

        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255, 255, 255, .4);
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin .6s linear infinite;
            vertical-align: middle
        }

        @keyframes spin {
            to {
                transform: rotate(360deg)
            }
        }

        /* RESULT */
        .result-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 12px
        }

        .result-item {
            background: #FFF8E7;
            border-radius: 10px;
            padding: 12px 14px;
            border: 1.5px solid #f0d9a0
        }

        .result-item .rl {
            font-size: 11px;
            color: #9ca3af;
            font-weight: 700
        }

        .result-item .rv {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 20px;
            color: #C0271A;
            margin-top: 2px;
            letter-spacing: .04em
        }

        .divider {
            border: none;
            border-top: 2px dashed #f0d9a0;
            margin: 8px 0
        }

        .sheet-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            color: #C0271A;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            border-bottom: 2px solid #f0d9a0;
            padding-bottom: 1px;
            transition: border-color .15s
        }

        .sheet-link:hover {
            border-color: #C0271A
        }

        .alert-info {
            background: #eff6ff;
            border: 1.5px solid #bfdbfe;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 13px;
            color: #1e40af
        }

        .section-sub {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 12px;
            letter-spacing: .1em;
            color: #9ca3af;
            margin-bottom: 10px
        }

        @media(max-width:600px) {
            .stat-row {
                grid-template-columns: 1fr 1fr
            }

            .result-grid {
                grid-template-columns: 1fr
            }

            .cabang-table {
                font-size: 12px
            }
        }
    </style>

    <div class="page-wrap">

        {{-- TOPBAR --}}
        <div class="topbar">
            <div>
                <h1>Sync Google Sheets</h1>
                <p>Kirim laporan penjualan & stok ke spreadsheet — per cabang atau semua cabang</p>
            </div>
            <a href="{{ url()->previous() }}" class="btn-back">← Kembali</a>
        </div>

        {{-- STATISTIK HARI INI — SEMUA CABANG --}}
        <div class="card">
            <div class="card-title">Ringkasan Hari Ini — Semua Cabang</div>
            <div class="stat-row">
                <div class="stat-card">
                    <div class="s-label">Total Penjualan</div>
                    <div class="s-val s-green">Rp{{ number_format($statsAll['today_sales'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="stat-card">
                    <div class="s-label">Shift 1</div>
                    <div class="s-val s-red">Rp{{ number_format($statsAll['shift1_sales'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="stat-card">
                    <div class="s-label">Shift 2</div>
                    <div class="s-val s-amber">Rp{{ number_format($statsAll['shift2_sales'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="stat-card">
                    <div class="s-label">Menu Terjual</div>
                    <div class="s-val s-amber">{{ $statsAll['today_items'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        {{-- STATISTIK PER CABANG --}}
        <div class="card">
            <div class="card-title">Performa Per Cabang — Hari Ini</div>
            @if ($statsPerCabang->isEmpty())
                <p style="color:#9ca3af;font-size:13px">Belum ada data cabang.</p>
            @else
                <div style="overflow-x:auto">
                    <table class="cabang-table">
                        <thead>
                            <tr>
                                <th>Cabang</th>
                                <th style="text-align:right">Transaksi</th>
                                <th style="text-align:right">Shift 1</th>
                                <th style="text-align:right">Shift 2</th>
                                <th style="text-align:right">Item Terjual</th>
                                <th style="text-align:right">Total Penjualan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($statsPerCabang as $s)
                                <tr>
                                    <td>
                                        <span class="cabang-badge">{{ $s['nama'] }}</span>
                                    </td>
                                    <td style="text-align:right;font-weight:800;color:#C0271A">{{ $s['trx'] }}</td>
                                    <td style="text-align:right;font-weight:800;color:#059669">
                                        Rp{{ number_format($s['shift1_sales'] ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align:right;font-weight:800;color:#059669">
                                        Rp{{ number_format($s['shift2_sales'] ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align:right;font-weight:700;color:#d97706">{{ $s['items'] }}</td>
                                    <td style="text-align:right;font-weight:800;color:#059669">
                                        Rp{{ number_format($s['sales'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- KONFIGURASI SPREADSHEET --}}
        <div class="card">
            <div class="card-title">Konfigurasi Spreadsheet</div>

            <div style="margin-bottom:16px">
                <label class="input-label">Spreadsheet ID</label>
                <input id="sheets-id-input" class="input-field" type="text"
                    placeholder="Contoh: 1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgVE2upms" oninput="saveSheetsId()">
                <div class="hint">Ambil dari URL: <code>spreadsheets/d/<strong>[ID]</strong>/edit</code></div>
            </div>
        </div>

        {{-- PILIH CABANG & PERIODE --}}
        <div class="card">
            <div class="card-title">Pilih Cabang</div>
            <div class="section-sub">Pilih cabang yang datanya ingin disinkronisasi</div>
            <div class="cabang-selector" id="cabang-selector">
                {{-- Tombol "Semua Cabang" --}}
                <button class="cabang-btn all-btn active" data-id="all" onclick="setCabang(this,'all')">
                    Semua Cabang
                </button>
                {{-- Tombol per cabang --}}
                @foreach ($cabangs as $cabang)
                    <button class="cabang-btn" data-id="{{ $cabang->idCabang }}"
                        onclick="setCabang(this,'{{ $cabang->idCabang }}')">
                        {{ $cabang->namaCabang }}
                    </button>
                @endforeach
            </div>

            <div id="cabang-info"
                style="margin-top:12px;padding:10px 14px;background:#FFF8E7;border-radius:10px;border:1.5px solid #f0d9a0;font-size:12px;color:#6b7280">
                <strong>Semua Cabang</strong> dipilih — data akan dikirim ke tab terpisah per cabang + tab ringkasan
                gabungan.
            </div>
        </div>

        {{-- PILIH PERIODE & SYNC --}}
        <div class="card">
            <div class="card-title">Periode & Sinkronisasi</div>

            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px">
                <button class="pay-btn active" id="periode-harian" onclick="setPeriode(this,'harian')">
                    Harian</button>
                <button class="pay-btn" id="periode-mingguan" onclick="setPeriode(this,'mingguan')">Mingguan</button>
                <button class="pay-btn" id="periode-bulanan" onclick="setPeriode(this,'bulanan')">🗓 Bulanan</button>
            </div>

            <div class="alert-info" style="margin-bottom:16px">
                Setiap cabang mendapat <strong>3 tab</strong>: <em>Ringkasan</em>, <em>Detail Transaksi</em>,
                dan <em>Mutasi Stok</em>. Jika sync <strong>Semua Cabang</strong>, akan ada tambahan tab
                <em>Ringkasan Semua Cabang</em>.
            </div>

            {{-- Preview yang akan disync --}}
            <div id="sync-preview"
                style="background:#FFF8E7;border-radius:12px;padding:14px 16px;border:2px solid #f0d9a0;margin-bottom:16px;font-size:13px">
                <div
                    style="font-family:'Bebas Neue',sans-serif;font-size:12px;letter-spacing:.1em;color:#C0271A;margin-bottom:8px">
                    Yang Akan Disinkronisasi</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    <span
                        style="background:#C0271A;color:#F5C518;padding:3px 12px;border-radius:99px;font-size:12px;font-weight:700"
                        id="preview-cabang">Semua Cabang</span>
                    <span
                        style="background:#f0d9a0;color:#9B1E13;padding:3px 12px;border-radius:99px;font-size:12px;font-weight:700"
                        id="preview-periode">Harian</span>
                </div>
            </div>

            <button id="btn-do-sync" class="btn-primary" onclick="doSync()">
                SYNC KE GOOGLE SHEETS SEKARANG
            </button>

            <div id="sync-status" class="sync-status" style="margin-top:14px"></div>

            {{-- Hasil sync --}}
            <div id="sync-result" style="display:none">
                <hr class="divider" style="margin-top:14px">
                <div
                    style="font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:.1em;color:#C0271A;margin-bottom:6px">
                    Hasil Sinkronisasi
                </div>
                <div id="res-cabang-label" style="font-size:12px;color:#6b7280;margin-bottom:10px"></div>
                <div class="result-grid">
                    <div class="result-item">
                        <div class="rl">Jumlah Transaksi</div>
                        <div class="rv" id="res-trx">—</div>
                    </div>
                    <div class="result-item">
                        <div class="rl">Item Terjual</div>
                        <div class="rv" id="res-items">—</div>
                    </div>
                    <div class="result-item">
                        <div class="rl">Total Diskon</div>
                        <div class="rv" id="res-disc">—</div>
                    </div>
                    <div class="result-item">
                        <div class="rl">Total Penjualan</div>
                        <div class="rv" id="res-sales">—</div>
                    </div>
                </div>
                <a id="open-sheet-link" href="#" target="_blank" class="sheet-link">Buka Spreadsheet →</a>
            </div>
        </div>

        {{-- PANDUAN --}}
        <div class="card">
            <div class="card-title">Panduan Penggunaan</div>
            <ol style="padding-left:18px;font-size:13px;color:#374151;line-height:2.2">
                <li>Isi <strong>Spreadsheet ID</strong> atau buat baru dengan tombol <strong>"Buat Spreadsheet
                        Baru"</strong>.</li>
                <li>Pilih <strong>cabang</strong> yang ingin disync — bisa satu cabang atau semua sekaligus.</li>
                <li>Pilih <strong>periode</strong>: Harian, Mingguan, atau Bulanan.</li>
                <li>Klik <strong>"Sync ke Google Sheets Sekarang"</strong>.</li>
                <li>Setiap cabang akan mendapat tab tersendiri. Sync <em>Semua Cabang</em> juga menambah tab
                    <strong>Ringkasan Semua Cabang</strong>.
                </li>
                <li>Data yang sudah ada di baris Ringkasan akan <strong>diperbarui</strong> (tidak duplikat).</li>
            </ol>
        </div>

    </div>{{-- /page-wrap --}}

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        const SYNC_URL = "{{ route('owner.sheets.sync') }}";
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        let selectedPeriode = 'harian';
        let selectedCabang = 'all';
        let selectedCabangLabel = 'Semua Cabang';

        // ── Init ──
        (function init() {
            document.getElementById('sheets-id-input').value = localStorage.getItem('owner_sheets_id') || '';
        })();

        function saveSheetsId() {
            localStorage.setItem('owner_sheets_id', document.getElementById('sheets-id-input').value.trim());
        }

        // ── Pilih Cabang ──
        function setCabang(btn, id) {
            document.querySelectorAll('.cabang-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedCabang = id;
            selectedCabangLabel = btn.textContent.trim();

            const info = document.getElementById('cabang-info');
            if (id === 'all') {
                info.innerHTML =
                    '<strong>Semua Cabang</strong> dipilih — data dikirim ke tab terpisah per cabang + tab ringkasan gabungan.';
            } else {
                info.innerHTML =
                    `Cabang <strong>${selectedCabangLabel}</strong> dipilih — data dikirim ke 3 tab khusus cabang ini.`;
            }

            document.getElementById('preview-cabang').textContent = selectedCabangLabel;
            document.getElementById('sync-result').style.display = 'none';
            hideStatus('sync-status');
        }

        // ── Pilih Periode ──
        function setPeriode(btn, periode) {
            document.querySelectorAll('.pay-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedPeriode = periode;
            const labels = {
                harian: 'Harian',
                mingguan: 'Mingguan',
                bulanan: 'Bulanan'
            };
            document.getElementById('preview-periode').textContent = labels[periode] || periode;
            document.getElementById('sync-result').style.display = 'none';
            hideStatus('sync-status');
        }

        function fmt(n) {
            return 'Rp' + Number(n || 0).toLocaleString('id-ID', {
                maximumFractionDigits: 0
            });
        }

        function showStatus(elId, type, msg) {
            const el = document.getElementById(elId);
            el.className = 'sync-status ' + (type === 'ok' ? 'sync-ok' : type === 'err' ? 'sync-err' : 'sync-wait');
            el.innerHTML = msg;
            el.style.display = '';
        }

        function hideStatus(elId) {
            document.getElementById(elId).style.display = 'none';
        }

        // ── Sync ke Sheets ──
        async function doSync() {
            const spreadsheetId = document.getElementById('sheets-id-input').value.trim();
            if (!spreadsheetId) {
                showStatus('sync-status', 'err', 'Masukkan Spreadsheet ID terlebih dahulu.');
                return;
            }
            const btn = document.getElementById('btn-do-sync');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner"></span> Mengirim data ke Google Sheets...';
            showStatus('sync-status', 'wait', `Mengirim data ${selectedCabangLabel} — ${selectedPeriode}...`);
            document.getElementById('sync-result').style.display = 'none';
            try {
                const res = await fetch(SYNC_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        spreadsheet_id: spreadsheetId,
                        periode: selectedPeriode,
                        cabang_id: selectedCabang
                    }),
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'Sync gagal.');

                showStatus('sync-status', 'ok', `${data.message}`);

                if (data.stats) {
                    document.getElementById('res-cabang-label').textContent = '' + (data.stats.cabang_label || '');
                    document.getElementById('res-trx').textContent = data.stats.jumlah_transaksi;
                    document.getElementById('res-items').textContent = data.stats.item_terjual;
                    document.getElementById('res-disc').textContent = fmt(data.stats.total_diskon);
                    document.getElementById('res-sales').textContent = fmt(data.stats.total_penjualan);
                }
                document.getElementById('open-sheet-link').href =
                    `https://docs.google.com/spreadsheets/d/${spreadsheetId}/edit`;
                document.getElementById('sync-result').style.display = '';
            } catch (err) {
                showStatus('sync-status', 'err', '' + (err.message || 'Terjadi kesalahan saat sync.'));
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'SYNC KE GOOGLE SHEETS SEKARANG';
            }
        }
    </script>

</x-layouts.app>
