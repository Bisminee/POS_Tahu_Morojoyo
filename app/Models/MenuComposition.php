<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuComposition extends Model
{
    protected $table = 'menu_compositions';

    protected $fillable = [
        'menu_id',
        'pcs_tahu_id',
        'jumlah_pakai',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'idMenu');
    }

    public function pcsTahu()
    {
        return $this->belongsTo(PcsTahu::class, 'pcs_tahu_id', 'id_pcs');
    }
}