<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController as Admin;
use App\Http\Controllers\PembeliController as Pembeli;
use App\Http\Controllers\BarangController as Barang;
use App\Http\Controllers\KategoriController as Kategori;
use App\Http\Controllers\TransaksiController as Transaksi;
use App\Http\Middleware\AdminRole;
use App\Http\Middleware\isLogin;

Route::get('/', [Pembeli::class, 'index'])->name('home');
Route::get('/search', [Pembeli::class, 'filter_index'])->name('search');

Route::prefix('login')->as('login.')->group(function () {
    Route::get('/', [Pembeli::class, 'form_login'])->name('page');
    Route::post('/auth', [Pembeli::class, 'auth_login'])->name('auth');
});

Route::prefix('daftar')->as('sign.')->group(function () {
    Route::get('/', [Pembeli::class, 'form_register'])->name('page');
    Route::post('/submit', [Pembeli::class, 'register'])->name('submit');
});

Route::prefix('profil')->as('profil.')->middleware(isLogin::class . ':pembeli')->group(function () {
    Route::get('/', [Pembeli::class, 'show_profile'])->name('show');
    Route::put('/ubah/{id}', [Pembeli::class, 'update_profile'])->name('update');
});

Route::post('/logout', [Pembeli::class, 'logout'])->middleware(isLogin::class . ':pembeli')->name('logout');

Route::prefix('kelola_barang')->as('kelola.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('kelola.login.show');
    });
    Route::prefix('login')->as('login.')->group(function () {

        Route::get('/', [Admin::class, 'form_login'])->name('show');

        Route::post('/auth', [Admin::class, 'auth_login'])->name('auth');
    });

    Route::prefix('add')->as('add.')->group(function () {
        Route::post('user', [Admin::class, 'add_user'])->name('user');
        Route::post('kategori', [Kategori::class, 'add_kategori'])->middleware(AdminRole::class . ':3')->name('kategori');
        Route::post('barang', [Barang::class, 'add_barang'])->middleware(AdminRole::class . ':3')->name('barang');
        Route::post('barang/bulk', [Barang::class, 'import_data'])->middleware(AdminRole::class . ':3')->name('bulk');
    });

    Route::prefix('delete')->as('delete.')->group(function () {
        Route::delete('user/{id}', [Admin::class, 'delete_user'])->name('user');
        Route::delete('kategori/{id}', [Kategori::class, 'delete_kategori'])->middleware(AdminRole::class . ':3')->name('kategori');
        Route::delete('barang/{id}', [Barang::class, 'delete_barang'])->middleware(AdminRole::class . ':3')->name('barang');
    });

    Route::prefix('update')->as('update.')->group(function () {
        Route::put('user/{id}', [Admin::class, 'update_user'])->name('user');
        Route::put('kategori/{id}', [Kategori::class, 'update_kategori'])->middleware(AdminRole::class . ':3')->name('kategori');
        Route::put('barang/{id}', [Barang::class, 'update_barang'])->middleware(AdminRole::class . ':3')->name('barang');
    });

    Route::prefix('panel')->as('panel.')->group(function () {
        Route::get('/super', [Admin::class, 'show_super'])->name('super');
        Route::get('/super/cari', [Admin::class, 'filter_admin'])->name('filter.super');



        Route::middleware(AdminRole::class . ':1')->prefix('cs1')->group(function () {
            Route::get('/', [Admin::class, 'show_cs1'])->name('cs1');
            Route::get('/get/konfirmasi/', [Transaksi::class, 'get_transaksi_all'])->name('get');
            Route::post('/batal/{id}', [Transaksi::class, 'transaksi_batal'])->name('cancel');
            Route::post('/konfirmasi/{id}', [Transaksi::class, 'confirm_transaksi'])->name('confirm');
        });

        Route::middleware(AdminRole::class . ':2')->group(function () {
            Route::get('/cs2', [Admin::class, 'show_cs2'])->name('cs2');
            Route::get('/get/proses', [Transaksi::class, 'get_proses'])->name('proses');
            Route::post('/kemas/{id}', [Barang::class, 'kemas_barang'])->name('kemas');
            Route::post('/kirim/{id}', [Barang::class, 'kirim_barang'])->name('kirim');
            Route::post('/sampai/{id}', [Barang::class, 'sampai_barang'])->name('sampai');
        });

        Route::middleware(AdminRole::class . ':3')->group(function () {
            Route::get('/admin', [Admin::class, 'show_admin'])->name('admin');
            Route::get('/kategori', [Kategori::class, 'show_kategori'])->name('kategori');
            Route::get('/barang', [Barang::class, 'show_barang'])->name('barang');
            Route::get('/kategori/cari', [Kategori::class, 'filter_kategori'])->name('filter.kategori');
            Route::get('/barang/cari', [Barang::class, 'filter_barang'])->name('filter.barang');
            Route::get('/barang/download', [Barang::class, 'bulk_template'])->name('template');
        });
    });

    Route::post('/logout', [Admin::class, 'logout'])->name('logout');
});

Route::prefix('keranjang')->as('keranjang.')->middleware(isLogin::class . ':pembeli')->group(function () {
    Route::get('/', [Pembeli::class, 'show_cart'])->name('show');
    Route::post('/tambah/{barang_id}', [Pembeli::class, 'add_cart'])->name('add');
    Route::post('/kurang/{barang_id}', [Pembeli::class, 'decreased_cart'])->name('dec');
});

Route::prefix('transaksi')->as('transaksi.')->middleware(isLogin::class . ':pembeli')->group(function () {
    Route::get('/', [Transaksi::class, 'transaksi_show'])->name('show');
    Route::post('/checkout', [Transaksi::class, 'transaksi_create'])->name('check');
    Route::post('/batal/{id}', [Transaksi::class, 'transaksi_remove'])->name('remove');
    Route::post('/unggah/{id}', [Transaksi::class, 'transaksi_unggah_bukti'])->name('upload');
    Route::get('/get/{id}', [Transaksi::class, 'get_transaksi'])->name('get');
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