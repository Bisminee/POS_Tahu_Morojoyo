<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'karyawan_id',
        'cabang_id',
        'sesi',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'toleransi_menit',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'idKaryawan');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'idCabang');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function hitungStatusMasuk(Carbon $jamMasukAktual): array
    {
        $batasWaktu = Carbon::parse(
            Carbon::parse($this->tanggal)->format('Y-m-d') . ' ' . $this->jam_mulai
        );

        $batasTelat = $batasWaktu->copy()->addMinutes($this->toleransi_menit);

        if ($jamMasukAktual->lte($batasTelat)) {
            return [
                'status' => 'tepat_waktu',
                'menit' => 0
            ];
        }

        return [
            'status' => 'telat',
            'menit' => (int) $jamMasukAktual->diffInMinutes($batasWaktu)
        ];
    }
}