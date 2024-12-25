<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PembeliController;

// Route::get('/', function () {
//     return view('pages/landing');
// });

Route::get('/', [PembeliController::class, 'index'])->name('home');

Route::prefix('login')->as('login.')->group(function () {
    Route::get('/', [PembeliController::class, 'form_login'])->name('page');
    Route::post('/auth', [PembeliController::class, 'auth_login'])->name('auth');
});

Route::prefix('daftar')->as('sign.')->group(function () {
    Route::get('/', [PembeliController::class, 'form_register'])->name('page');

    Route::post('/submit', [PembeliController::class, 'register'])->name('submit');
});

Route::prefix('profil')->as('profil.')->group(function () {
    Route::get('/', [PembeliController::class, 'show_profile'])->name('show');

    Route::put('/ubah/{id}', [PembeliController::class, 'update_profile'])->name('update');
});

Route::post('/logout', [PembeliController::class, 'logout'])->name('logout');


// $ar = [endpoint, method, controllerFunction, nameRoute]

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
//     return PembeliController::class;
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