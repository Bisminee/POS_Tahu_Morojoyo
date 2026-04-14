<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karyawan extends Model
{
    protected $table = 'karyawans';
    protected $primaryKey = 'idKaryawan';

    protected $fillable = [
        'nama',
        'no_telp',
    ];

    public function cabangs(): HasMany
    {
        return $this->hasMany(Cabang::class, 'Karyawan', 'idKaryawan');
    }
}