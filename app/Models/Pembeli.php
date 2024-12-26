<?php

// use Illuminate\Database\Eloquent\Model;
namespace App\Models;

use Illuminate\Foundation\Auth\User as Auth;

class Pembeli extends Auth
{
    //

    protected $table = 'pembeli';

    protected $fillable = ['nama', 'username', 'password'];

    protected $hidden = ['password', 'remember_token'];
}
