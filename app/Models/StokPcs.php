<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokPcs extends Model
{
    protected $table = 'stok_pcs';
    protected $primaryKey = 'idStokPcs';

    protected $fillable = [
        'id_cabang',
        'id_pcs_tahu',
        'jumlah_stok',
    ];

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'id_cabang', 'idCabang');
    }

    public function pcsTahu(): BelongsTo
    {
        return $this->belongsTo(PcsTahu::class, 'id_pcs_tahu', 'id_pcs');
    }
}