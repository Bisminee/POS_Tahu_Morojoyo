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
        'metodePayment',
        'harga',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'idMenu', 'idMenu');
    }
}