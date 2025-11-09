<?php

use App\Http\Controllers\Api\V1\AttachmentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\KkprController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\PermohonanController;
use App\Http\Controllers\Api\V1\SitrRdtrController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'throttle:20,1'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::apiResource('/permohonan/sitr-rdtr', SitrRdtrController::class)->parameters([
            'sitr-rdtr' => 'permohonan'
        ]);
        Route::post('/permohonan/sitr-rdtr/{permohonan}/generate-docs', [
            SitrRdtrController::class, 'generateDocuments'
        ])->name('api.sitr-rdtr.generate');

        Route::apiResource('/permohonan/kkpr', KkprController::class)->parameters([
            'kkpr' => 'permohonan'
        ]);
        Route::post('/permohonan/kkpr/{permohonan}/generate-docs', [
            KkprController::class, 'generateDocuments'
        ])->name('api.kkpr.generate');

        Route::get('/permohonan/tte-queue', [PermohonanController::class, 'tteQueue'])
                ->name('api.permohonan.tteQueue');

        Route::get('/permohonan/{permohonan}/timeline', [PermohonanController::class, 'timeline'])
                ->name('api.permohonan.timeline');

        Route::post('/permohonan/{permohonan}/update-tte', [PermohonanController::class, 'updateSignedDocument'])
                ->name('api.permohonan.update-tte');

        Route::post('/permohonan/{permohonan}/update-sk', [PermohonanController::class, 'updateSkDocument'])
                ->name('api.permohonan.update-sk');

        Route::post('/attachments', [AttachmentController::class, 'store'])->name('api.attachments.store');
    });
    Route::prefix('location')->group(function () {
        Route::get('/provinces', [LocationController::class, 'provinces']);
        Route::get('/regencies/{provinceId}', [LocationController::class, 'regencies']);
        Route::get('/districts/{regencyId}', [LocationController::class, 'districts']);
        Route::get('/villages/{districtId}', [LocationController::class, 'villages']);
    });
});
