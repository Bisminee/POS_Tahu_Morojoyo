<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiStok extends Model
{
    protected $table = 'mutasi_stok';
    protected $primaryKey = 'idMutasi';

    protected $fillable = [
        'id_cabang',
        'id_pcs_tahu',
        'tipe',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'keterangan',
    ];

    protected $casts = [
        'tipe' => 'string',
        'jumlah' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_sesudah' => 'integer',
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
