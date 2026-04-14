<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PcsTahu extends Model
{
    protected $table = 'pcs_tahus';
    protected $primaryKey = 'idPcs';

    protected $fillable = [
        'namaPcs',
    ];

    public function stokPcs(): HasMany
    {
        return $this->hasMany(StokPcs::class, 'idPcs', 'idPcs');
    }

    public function menuDetails(): HasMany
    {
        return $this->hasMany(MenuDetail::class, 'idPcs', 'idPcs');
    }
}