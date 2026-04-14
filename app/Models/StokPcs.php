<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokPcs extends Model
{
    protected $table = 'stok_pcs';
    protected $primaryKey = 'idStokPcs';

    protected $fillable = [
        'idCabang',
        'idPcs',
        'jumlahStok',
    ];

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'idCabang', 'idCabang');
    }

    public function pcsTahu(): BelongsTo
    {
        return $this->belongsTo(PcsTahu::class, 'idPcs', 'idPcs');
    }
}