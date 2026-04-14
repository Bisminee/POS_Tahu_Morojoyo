<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $table = 'menus';
    protected $primaryKey = 'idMenu';

    protected $fillable = [
        'namaMenu',
        'deskripsi',
    ];

    public function hargas(): HasMany
    {
        return $this->hasMany(Harga::class, 'idMenu', 'idMenu');
    }

    public function menuDetails(): HasMany
    {
        return $this->hasMany(MenuDetail::class, 'idMenu', 'idMenu');
    }

    public function getDeskripsiAttribute()
    {
        return $this->menuDetails
            ->map(function ($detail) {
                return optional($detail->pcsTahu)->nama_pcs . ' (' . $detail->jumlah_pcs . ')';
            })
            ->implode(', ');
    }
}
