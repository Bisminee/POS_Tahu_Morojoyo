<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'shift_id',
        'user_id',
        'jam_masuk',
        'status_masuk',
        'telat_menit',
        'foto_absen',
        'face_confidence',
        'jam_keluar',
        'jenis_keluar',
        'digantikan_oleh',
        'catatan',
    ];

    protected $casts = [
        'jam_masuk' => 'datetime',
        'jam_keluar' => 'datetime',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'idKaryawan');
    }

    public function pengganti()
    {
        return $this->belongsTo(Karyawan::class, 'digantikan_oleh', 'idKaryawan');
    }
}