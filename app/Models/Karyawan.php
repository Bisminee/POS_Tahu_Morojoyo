<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawans';

    protected $primaryKey = 'idKaryawan';

    public $incrementing = true;

    protected $keyType = 'int';

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
        'face_descriptor' => 'array',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'idCabang');
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class, 'karyawan_id', 'idKaryawan');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'karyawan_id', 'idKaryawan');
    }
}