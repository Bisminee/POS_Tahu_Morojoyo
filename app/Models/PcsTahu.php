<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PcsTahu extends Model
{
    protected $table = 'pcs_tahus';

    protected $primaryKey = 'id_pcs';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'nama_pcs',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function stokPcs(): HasMany
    {
        return $this->hasMany(StokPcs::class, 'id_pcs_tahu', 'id_pcs');
    }

    public function menuDetails(): HasMany
    {
        return $this->hasMany(MenuDetail::class, 'id_pcs', 'id_pcs');
    }

    public function menuCompositions(): HasMany
    {
        return $this->hasMany(MenuComposition::class, 'pcs_tahu_id', 'id_pcs');
    }
}