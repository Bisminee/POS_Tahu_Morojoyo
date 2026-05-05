<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Harga extends Model
{
    protected $table = 'hargas';
    protected $primaryKey = 'idHarga';

    protected $fillable = [
        'idMenu',
        'harga_normal',
        'harga_gofood',
        'harga_shopeefood',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'idMenu', 'idMenu');
    }
}