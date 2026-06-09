{{-- resources/views/emails/data-clear-alert.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; background: #f4f5f7; margin: 0; padding: 20px; }
        .card { background: #fff; border-radius: 12px; padding: 32px; 
                max-width: 560px; margin: 0 auto; }
        .badge-danger { 
            background: #fef2f2; color: #991b1b; 
            padding: 16px 20px; border-radius: 10px;
            border-left: 4px solid #ef4444; 
            margin: 20px 0; font-size: 14px; line-height: 1.6;
        }
        .clear-date {
            font-size: 22px; font-weight: 700; 
            color: #dc2626; text-align: center;
            margin: 20px 0; padding: 16px;
            background: #fef2f2; border-radius: 10px;
        }
        .footer { margin-top: 24px; font-size: 12px; 
                  color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
<div class="card">
    <h2 style="margin:0 0 4px;color:#111827">⚠️ Peringatan Penghapusan Data</h2>
    <p style="color:#6b7280;font-size:13px;margin:0 0 20px">
        Dikirim otomatis pada {{ now()->translatedFormat('d F Y, H:i') }}
    </p>

    <div class="badge-danger">
        Data penjualan tahun <strong>{{ $year }}</strong> akan dihapus permanen 
        7 hari sejak email ini dikirim. Pastikan kamu sudah menyimpan atau 
        mengekspor data yang diperlukan sebelum tanggal tersebut.
    </div>

    <p style="font-size:14px;color:#374151;text-align:center">
        Jadwal penghapusan data:
    </p>

    <div class="clear-date">
        🗑️ {{ $clearDate }}
    </div>

    <div class="badge-danger">
        <strong>Data yang akan dihapus:</strong><br>
        • Data transaksi & item transaksi tahun {{ $year }}<br>
        • Data stok tahun {{ $year }}<br>
        • Riwayat harga tahun {{ $year }}<br>
        • Foto absensi tahun {{ $year }}
    </div>

    <div class="footer">
        Email ini dikirim otomatis oleh sistem. Harap tidak membalas email ini.
    </div>
</div>
</body>
</html>