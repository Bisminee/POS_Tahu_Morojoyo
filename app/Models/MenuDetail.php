<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuDetail extends Model
{
    protected $table = 'menu_details';
    protected $primaryKey = 'idDetail';

    protected $fillable = [
        'idMenu',
        'idPcs',
        'jumlahPcs',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'idMenu', 'idMenu');
    }

    public function pcsTahu(): BelongsTo
    {
        return $this->belongsTo(PcsTahu::class, 'idPcs', 'idPcs');
    }
}