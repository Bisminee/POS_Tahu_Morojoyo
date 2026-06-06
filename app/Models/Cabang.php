<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabang extends Model
{
    protected $table = 'cabangs';

    protected $primaryKey = 'idCabang';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'namaCabang',
        'alamat',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function stokPcs(): HasMany
    {
        return $this->hasMany(StokPcs::class, 'id_cabang', 'idCabang');
    }
}