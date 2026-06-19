<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuDetail extends Model
{
    protected $table = 'menu_details';

    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'idMenu',
        'id_pcs',
        'jumlah_pcs',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'idMenu', 'idMenu');
    }

    public function pcsTahu()
    {
        return $this->belongsTo(PcsTahu::class, 'id_pcs', 'id_pcs');
    }
}