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
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}