<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\MenuComposition;

class Menu extends Model
{
    protected $table = 'menus';
    protected $primaryKey = 'idMenu';

    protected $fillable = [
        'namaMenu',
    ];

    public function hargas(): HasMany
    {
        return $this->hasMany(Harga::class, 'idMenu', 'idMenu');
    }

    public function compositions()
    {
        return $this->hasMany(MenuComposition::class, 'menu_id', 'idMenu');
    }
    public function menuDetails(): HasMany
    {
        return $this->hasMany(MenuDetail::class, 'idMenu', 'idMenu');
        }
        
        // ✅ FIX #2: Tambahkan relationLoaded() guard
        public function getDeskripsiAttribute(): string
        {
            // Jika relasi belum di-load, jangan trigger query baru
            // (mencegah N+1 di konteks yang tidak punya eager load)
        if (! $this->relationLoaded('menuDetails')) {
            return '';
            }
            
            return $this->menuDetails
            ->filter(fn($detail) => $detail->pcsTahu !== null)
            ->map(fn($detail) => $detail->pcsTahu->nama_pcs . ' (' . $detail->jumlah_pcs . ')')
            ->implode(', ');
            }
            
        }
