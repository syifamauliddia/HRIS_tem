<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (TALENT Frontend)
|--------------------------------------------------------------------------
*/

// 1. Route Default (Redirect otomatis ke Login)
Route::get('/', function () {
    return redirect('/login');
});

// 2. Route Authentication
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Route Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
});

// Route Akses Responsibility
Route::get('/change-akses', function () {
    return view('change_akses');
});

/*
|--------------------------------------------------------------------------
| Halaman Aplikasi Utama
|--------------------------------------------------------------------------
*/

// 3. Route Group: Master Penilaian
Route::prefix('master-penilaian')->group(function () {
    
    // Halaman Tabel List Data (URL: /master-penilaian)
    Route::get('/', function () {
        return view('penilaian.master_index');
    });
    
    // Halaman Form Tambah Data (URL: /master-penilaian/create)
    Route::get('/create', function () {
        return view('penilaian.master_create');
    });

});


// 4. Route Group: Transaksi Penilaian 360
Route::prefix('penilaian-360')->group(function () {
    
    // Halaman Tabel Transaksi (URL: /penilaian-360)
    Route::get('/', function () {
        return view('penilaian.transaksi_360_index');
    });
    
    // (Opsional) Persiapan jika nanti ada form create untuk 360
    // Route::get('/create', function () {
    //     return view('penilaian.transaksi_360_create');
    // });

});

Route::get('/notification', function () {
    return view('notification');
});

// Route Transaksi Absensi Karyawan
Route::prefix('absensi')->group(function () {
    Route::get('/', function () {
        return view('absensi.index');
    });
    Route::get('/create', function () {
        return view('absensi.form');
    });
    Route::get('/{id}', function () {
        return view('absensi.form');
    });
});