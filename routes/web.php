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
    Route::get('/', function () {
        return redirect()->route('kelola.login.show');
    });
    Route::prefix('login')->as('login.')->group(function () {

        Route::get('/', [Admin::class, 'form_login'])->name('show');

        Route::post('/auth', [Admin::class, 'auth_login'])->name('auth');
    });

    Route::prefix('add')->as('add.')->group(function () {
        Route::post('user', [Admin::class, 'add_user'])->name('user');
        Route::post('kategori', [Admin::class, 'add_kategori'])->middleware(AdminRole::class . ':3')->name('kategori');
        Route::post('barang', [Admin::class, 'add_barang'])->middleware(AdminRole::class . ':3')->name('barang');
        Route::post('barang/bulk', [Admin::class, 'import_data'])->middleware(AdminRole::class . ':3')->name('bulk');
    });

    Route::prefix('delete')->as('delete.')->group(function () {
        Route::delete('user/{id}', [Admin::class, 'delete_user'])->name('user');
        Route::delete('kategori/{id}', [Admin::class, 'delete_kategori'])->middleware(AdminRole::class . ':3')->name('kategori');
        Route::delete('barang/{id}', [Admin::class, 'delete_barang'])->middleware(AdminRole::class . ':3')->name('barang');
    });
    Route::prefix('update')->as('update.')->group(function () {
        Route::put('user/{id}', [Admin::class, 'update_user'])->name('user');
        Route::put('kategori/{id}', [Admin::class, 'update_kategori'])->middleware(AdminRole::class . ':3')->name('kategori');
        Route::put('barang/{id}', [Admin::class, 'update_barang'])->middleware(AdminRole::class . ':3')->name('barang');
    });

    Route::prefix('panel')->as('panel.')->group(function () {
        Route::get('/super', [Admin::class, 'show_super'])->name('super');
        Route::get('/super/cari', [Admin::class, 'filter_admin'])->name('filter.super');
        Route::get('/admin', [Admin::class, 'show_admin'])->middleware(AdminRole::class . ':3')->name('admin');
        Route::get('/kategori', [Admin::class, 'show_kategori'])->middleware(AdminRole::class . ':3')->name('kategori');
        Route::get('/barang', [Admin::class, 'show_barang'])->middleware(AdminRole::class . ':3')->name('barang');
        Route::get('/kategori/cari', [Admin::class, 'filter_kategori'])->middleware(AdminRole::class . ':3')->name('filter.kategori');
        Route::get('/barang/cari', [Admin::class, 'filter_barang'])->middleware(AdminRole::class . ':3')->name('filter.barang');
        Route::get('/barang/download', [Admin::class, 'bulk_template'])->middleware(AdminRole::class . ':3')->name('template');
        Route::get('/cs1', [Admin::class, 'show_cs1'])->middleware(AdminRole::class . ':1')->name('cs1');
        Route::get('/cs2', [Admin::class, 'show_cs2'])->middleware(AdminRole::class . ':2')->name('cs2');
    });

    Route::post('/logout', [Admin::class, 'logout'])->name('logout');
});

Route::prefix('keranjang')->as('keranjang.')->group(function () {
    Route::post('/tambah/{barang_id}', [Pembeli::class, 'add_cart'])->name('add');
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