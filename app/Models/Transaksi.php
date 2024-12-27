<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    //
    protected $table = 'transaksi';

    protected $fillable = ['pembeli_id', 'status', 'bukti'];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class);
    }
    public function transaksiDetail()
    {
        return $this->hasMany(TransaksiDetail::class);
    }
}
