<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabang extends Model
{
    protected $table = 'cabangs';
    protected $primaryKey = 'idCabang';

    protected $fillable = [
        'Karyawan',
        'Alamat',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'Karyawan', 'idKaryawan');
    }

    public function stokPcs(): HasMany
    {
        return $this->hasMany(StokPcs::class, 'id_cabang', 'idCabang');
    }
}