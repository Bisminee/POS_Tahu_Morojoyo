<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Identitas extends Model
{
    protected $table = 'identitas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_brand',
        'deskripsi_brand',
        'nomor_whatsapp',
        'nama_ig',
        'link_wa',
        'link_ig',
        'jam_buka',
        'jam_tutup',
        'logo',
        'promo',
    ];
}