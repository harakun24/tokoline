<?php

use App\Http\Controllers\AdminController as Admin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PembeliController as Pembeli;
use App\Http\Middleware\AdminRole;
use App\Http\Middleware\isLogin;

Route::get('/', [Pembeli::class, 'index'])->name('home');

Route::prefix('login')->as('login.')->group(function () {

    Route::get('/', [Pembeli::class, 'form_login'])->middleware(isLogin::class . ':pembeli')->name('page');

    Route::post('/auth', [Pembeli::class, 'auth_login'])->name('auth');
});

Route::prefix('daftar')->as('sign.')->group(function () {
    Route::get('/', [Pembeli::class, 'form_register'])->name('page');

    Route::post('/submit', [Pembeli::class, 'register'])->name('submit');
});

Route::prefix('profil')->as('profil.')->group(function () {
    Route::get('/', [Pembeli::class, 'show_profile'])->name('show');

    Route::put('/ubah/{id}', [Pembeli::class, 'update_profile'])->name('update');
});

Route::post('/logout', [Pembeli::class, 'logout'])->name('logout');

Route::prefix('kelola_gudang')->as('kelola.')->group(function () {
    Route::prefix('login')->as('login.')->group(function () {

        Route::get('/', [Admin::class, 'form_login'])->name('show');

        Route::post('/auth', [Admin::class, 'auth_login'])->name('auth');
    });

    Route::prefix('add')->as('add.')->group(function () {
        Route::post('user', [Admin::class, 'add_user'])->name('user');
    });

    Route::prefix('delete')->as('delete.')->group(function () {
        Route::delete('user/{id}', [Admin::class, 'delete_user'])->name('user');
    });
    Route::prefix('update')->as('update.')->group(function () {
        Route::put('user/{id}', [Admin::class, 'update_user'])->name('user');
    });

    Route::prefix('panel')->as('panel.')->group(function () {
        Route::get('/super', [Admin::class, 'show_super'])->name('super');

        Route::get('/admin', [Admin::class, 'show_admin'])->middleware(AdminRole::class . ':3')->name('admin');

        Route::get('/cs1', [Admin::class, 'show_cs1'])->middleware(AdminRole::class . ':1')->name('cs1');

        Route::get('/cs2', [Admin::class, 'show_cs2'])->middleware(AdminRole::class . ':2')->name('cs2');
    });

    Route::post('/logout', [Admin::class, 'logout'])->name('logout');
});


// //$ar = [endpoint, method, controllerFunction, nameRoute]

// function rt($controller, $ar)
// {
//     switch ($ar[1]) {
//         case 'get':
//             Route::get($ar[0], [$controller, $ar[2]])->name($ar[3]);
//             break;
//         case 'post':
//             Route::post($ar[0], [$controller, $ar[2]])->name($ar[3]);
//             break;
//         case 'put':
//             Route::put($ar[0], [$controller, $ar[2]])->name($ar[3]);
//             break;
//         case 'delete':
//             Route::delete($ar[0], [$controller, $ar[2]])->name($ar[3]);
//             break;
//     }
// }

// function pembeli()
// {
//     return Pembeli::class;
// };

// function sub($prefix, $alias, $callback)
// {
//     Route::prefix($prefix)->as($alias)->group($callback);
// }

// rt(pembeli(), ['/', 'get', 'index', 'home']);

// sub(
//     'login',
//     'login.',
//     function () {
//         rt(pembeli(), ['/', 'get', 'form_login', 'page']);
//         rt(pembeli(), ['/auth', 'post', 'auth_login', 'auth']);
//     }
// );
// sub(
//     'daftar',
//     'sign.',
//     function () {
//         rt(pembeli(), ['/', 'get', 'form_register', 'page']);
//         rt(pembeli(), ['/submit', 'post', 'register', 'submit']);
//     }
// );


// rt(pembeli(), ['/logout', 'get', 'logout', 'logout']);