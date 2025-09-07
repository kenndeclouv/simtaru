<?php

/**
 * Dokumentasi dan Aturan Penulisan Route
 *
 * 1. Penulisan nama alias Class Controller
 *    Gunakan format RoleFitur untuk penamaan alias controller.
 *    Contoh: jika controller tersebut mengelola fitur home untuk role SuperAdmin,
 *    maka penamaannya adalah SuperAdminHome. Pastikan untuk mengikuti konvensi ini
 *    agar mudah dikenali dan dikelompokkan berdasarkan peran.
 *
 * 2. Menambah Route untuk Role Baru
 *    Ketika menambahkan route untuk role baru, gunakan format berikut:
 *
 *    Route::prefix('role')->name('role.')->middleware(['auth', 'can:isRole'])->group(function () {
 *        Route::redirect('/', '/role/home', 301); // Redirect ke halaman home role
 *        Route::get('home', [RoleHome::class, 'index'])->name('home'); // Mendefinisikan route home
 *        // Di sini, Kamu dapat menambahkan semua grup fitur lainnya yang relevan untuk role ini.
 *    });
 *
 * 3. Menambah Fitur di Dalam Route Role
 *    Jika Kamu ingin menambahkan fitur di dalam route untuk role tertentu,
 *    gunakan format berikut:
 *
 *    Route::prefix('fiture')->name('fiture.')->group(function () {
 *        // Definisikan semua metode HTTP yang diperlukan seperti get, post, put, delete
 *    });
 *
 * 4. Format penulisan (PENTING!!!)
 *
 *  Route::prefix('lowercase')->name('lowercase.')->middleware(['auth'],['can:isPascalCase'])->group(function () {
 *      Route::get('/', [PascalCase::class, 'camelCase'])->name('kebab-case');
 *      Route::put('lowercase/{camelCase}', [PascalCase::class, 'camelCase'])->name('kebab-case');
 *  });
 * 
 *  © 2025 by kenndeclouv
 *  https://kenn.my.id
 */

// DEPENDENCIES

use App\Http\Controllers\KeyStorageController;
use Illuminate\Support\Facades\Route;

// FEATURE CONTROLLER
use App\Http\Controllers\PermohonanController;
use App\Http\Controllers\TemplateDocsController;
use App\Http\Controllers\RoleController;

/**
 * **Root**
 */
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::resource('template', TemplateDocsController::class);
Route::resource('permohonan', PermohonanController::class);
Route::resource('roles', RoleController::class);
Route::resource('key-storages', KeyStorageController::class);
