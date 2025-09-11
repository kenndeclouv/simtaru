<?php
// DEPENDENCIES

use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\KeyStorageController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;


// FEATURE CONTROLLER
use App\Http\Controllers\PermohonanController;
use App\Http\Controllers\TemplateDocsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RouteListController;
use App\Http\Controllers\UserController;

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
Route::post('permohonan/{permohonan}/generate-documents', [PermohonanController::class, 'generateDocuments'])->name('permohonan.generateDocuments');
Route::put('permohonan/{permohonan}/status', [PermohonanController::class, 'status'])->name('permohonan.status');

Route::resource('roles', RoleController::class);

Route::resource('key-storages', KeyStorageController::class);

Route::resource('users', UserController::class);

Route::prefix('logs')->name('logs.')->group(function () {
    Route::get('/', [LogController::class, 'index'])->name('index');
    Route::get('/show/{filename}', [LogController::class, 'show'])->name('show');
    Route::delete('/delete/{filename}', [LogController::class, 'destroy'])->name('destroy');
    Route::get('/download/{filename}', [LogController::class, 'download'])->name('download');
});

Route::get('/performance', [PerformanceController::class, 'index'])->name('performance.index');
Route::get('/route-list', [RouteListController::class, 'index'])->name('route-list.index');

Route::get('/audit-trail', [AuditTrailController::class, 'index'])->name('audit.index');