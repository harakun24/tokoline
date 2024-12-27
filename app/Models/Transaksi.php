<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    //
    protected $table = 'transaksi';

    protected $fillable = ['pembeli_id', 'status', 'bukti'];
    public function barang()
    {
        return $this->belongsToMany(Barang::class, 'transaksi_baang')->withPivot('jumlah')->withTimestamps();
    }
}
