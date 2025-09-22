<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\KkprController;
use App\Http\Controllers\Api\V1\SitrRdtrController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'throttle:20,1'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::apiResource('/permohonan/sitr-rdtr', SitrRdtrController::class)->parameters([
            'sitr-rdtr' => 'permohonan' // Alias parameter agar Route Model Binding tetap bekerja
        ]);
        Route::apiResource('/permohonan/kkpr', KkprController::class)->parameters([
            'kkpr' => 'permohonan' // Alias parameter agar Route Model Binding tetap bekerja
        ]);
    });
});
