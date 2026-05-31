<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    protected $table = 'cabangs';
    protected $primaryKey = 'idCabang';
    protected $fillable = ['namaCabang', 'alamat',];
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'cabang_id', 'idCabang');
    }
    public function shifts()
    {
        return $this->hasMany(Shift::class, 'cabang_id', 'idCabang');
    }
}
