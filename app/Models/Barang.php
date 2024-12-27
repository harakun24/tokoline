<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    //
    protected $table = 'barang';
    protected $fillable = ['nama', 'kategori_id', 'harga', 'stok', 'cover'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function transaksi()
    {
        return $this->belongsToMany(Transaksi::class, 'transaksi_barang')->withPivot('jumlah')->withTimestamps();
    }
}
