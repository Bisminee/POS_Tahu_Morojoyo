<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\MenuComposition;
use App\Models\MenuDetail;

class Menu extends Model
{
    protected $table = 'menus';
    protected $primaryKey = 'idMenu';

    protected $fillable = [
        'namaMenu'
        // 'deskripsi' JANGAN ada di sini
    ];

    // ✅ FIX #1: Tambahkan $appends
    protected $appends = ['deskripsi'];

    public function hargas(): HasMany
    {
        return $this->hasMany(Harga::class, 'idMenu', 'idMenu');
    }
    public function menuDetails(): HasMany
    {
        return $this->hasMany(MenuDetail::class, 'id_detail', 'idMenu');
    }

    public function compositions()
    {
        return $this->hasMany(MenuComposition::class, 'menu_id', 'idMenu');
    }

    public function getDeskripsiAttribute()
    {
        return $this->compositions
            ->map(
                fn($item) => ($item->pcsTahu->nama ?? '-') . ' (' . ($item->jumlah_pakai ?? 0) . ')'
            )
            ->implode(', ');
    }
}
