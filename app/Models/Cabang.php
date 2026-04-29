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
        'namaCabang',
        'karyawan_id',
        'alamat',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'idKaryawan');
    }

    public function stokPcs(): HasMany
    {
        return $this->hasMany(StokPcs::class, 'id_cabang', 'idCabang');
    }
}