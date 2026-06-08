<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $primaryKey = 'idKaryawan';

    protected $fillable = [
        'nama',
        'no_telp',
        'user_id',
        'cabang_id',
        'face_photo',
        'face_descriptor',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'idCabang');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'karyawan_id', 'idKaryawan');
    }
}