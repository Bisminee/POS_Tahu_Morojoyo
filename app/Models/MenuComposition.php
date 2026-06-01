<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuComposition extends Model
{
    protected $table = 'menu_compositions';

    protected $fillable = [
        'menu_id',
        'pcs_tahu_id',
        'jumlah_pakai',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'idMenu');
    }

    public function pcsTahu(): BelongsTo
    {
        return $this->belongsTo(PcsTahu::class, 'pcs_tahu_id', 'id_pcs');
    }
}