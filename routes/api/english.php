<?php

use App\Http\Controllers\Api\English\AuthController;
use App\Http\Controllers\Api\English\ReadingController;

Route::prefix('auth')->group(function () {
    Route::post('get-access-token', [AuthController::class, 'getAccessToken'])->name('english.auth.get-access-token');

    Route::prefix('reading')->group(function () {
        Route::get('download', [ReadingController::class, 'download'])->name('english.reading.download');
    });
});