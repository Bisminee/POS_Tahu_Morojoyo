<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\MenuDetail;

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
    public function menuDetails(): HasMany
    {
        return $this->hasMany(MenuDetail::class, 'id_detail', 'idMenu');
    }
}
