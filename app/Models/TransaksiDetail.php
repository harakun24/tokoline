<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    //
    protected $table = 'transaksi_detail';
    protected $fillable = ['keranjang_id', 'barang_id', 'jumlah'];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
