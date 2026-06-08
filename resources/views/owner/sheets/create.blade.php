@props(['title' => 'Buat Spreadsheet Baru'])

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
            max-width: 600px;
            margin: 0 auto;
            padding: 28px 20px;
            display: flex;
            flex-direction: column;
            gap: 20px
        }

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
        }

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
            transition: border-color .15s;
            margin-bottom: 10px
        }

        .input-field:focus {
            border-color: #C0271A
        }

        .input-label {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 13px;
            letter-spacing: .1em;
            color: #C0271A;
            display: block;
            margin-bottom: 7px
        }

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

        .alert-success {
            background: #f0fdf4;
            border: 1.5px solid #bbf7d0;
            color: #166534;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 16px;
            display: none
        }

        .alert-error {
            background: #fef2f2;
            border: 1.5px solid #fecaca;
            color: #991b1b;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 16px;
            display: none
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
    </style>

    <div class="page-wrap">

        {{-- TOPBAR --}}
        <div class="topbar">
            <div>
                <h1>✨ Buat Spreadsheet Baru</h1>
                <p>Buat Google Sheets baru untuk menyimpan laporan penjualan</p>
            </div>
            <a href="{{ route('owner.sheets.index') }}" class="btn-back">← Kembali</a>
        </div>

        {{-- FORM CREATE --}}
        <div class="card">
            <div class="card-title">📝 Detail Spreadsheet</div>

            <div id="alert-success" class="alert-success">
                ✅ <span id="success-msg"></span>
            </div>
            <div id="alert-error" class="alert-error">
                ❌ <span id="error-msg"></span>
            </div>

            <form id="create-form" onsubmit="handleSubmit(event)">
                @csrf

                <label class="input-label">Nama Spreadsheet</label>
                <input type="text" class="input-field" name="name" id="name" 
                    placeholder="Contoh: Laporan Penjualan Mei 2024" required>

                <label class="input-label">Email untuk Share Akses</label>
                <input type="email" class="input-field" name="share_to_email" id="share_to_email"
                    value="{{ auth()->user()->email }}" required>

                <button type="submit" class="btn-primary" id="btn-submit">
                    ✨ BUAT SPREADSHEET
                </button>
            </form>
        </div>

        {{-- INFO --}}
        <div class="card">
            <div class="card-title">ℹ️ Informasi</div>
            <ul style="padding-left:18px;font-size:13px;color:#374151;line-height:2">
                <li>Spreadsheet akan dibuat dengan nama yang Anda masukkan</li>
                <li>Akses akan dikirim ke email yang terdaftar</li>
                <li>Spreadsheet ID akan tersimpan otomatis</li>
                <li>Setelah dibuat, Anda bisa mulai sync data</li>
            </ul>
        </div>

    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        const CREATE_URL = "{{ route('owner.sheets.store') }}";
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        async function handleSubmit(event) {
            event.preventDefault();

            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('share_to_email').value.trim();
            const btn = document.getElementById('btn-submit');
            const form = document.getElementById('create-form');

            // Hide alerts
            document.getElementById('alert-success').style.display = 'none';
            document.getElementById('alert-error').style.display = 'none';

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner"></span> Membuat...';

            try {
                const res = await fetch(CREATE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: name,
                        share_to_email: email
                    })
                });

                const data = await res.json();

                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'Gagal membuat spreadsheet');
                }

                // Success
                document.getElementById('success-msg').textContent = data.message;
                document.getElementById('alert-success').style.display = '';
                form.reset();

                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = "{{ route('owner.sheets.index') }}";
                }, 2000);

            } catch (err) {
                document.getElementById('error-msg').textContent = err.message || 'Terjadi kesalahan';
                document.getElementById('alert-error').style.display = '';
            } finally {
                btn.disabled = false;
                btn.innerHTML = '✨ BUAT SPREADSHEET';
            }
        }
    </script>

</x-layouts.app>