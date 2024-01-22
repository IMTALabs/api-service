<?php

use App\Http\Controllers\Api\English\AuthController;
use App\Http\Controllers\Api\English\ReadingController;

Route::prefix('auth')->group(function () {
    Route::post('get-access-token', [AuthController::class, 'getAccessToken'])->name('english.auth.get-access-token');
});


Route::prefix('reading')->group(function () {
    Route::get('download', [ReadingController::class, 'download'])->name('english.reading.download');
});
Route::prefix('listening')->group(function () {
    Route::get('download', [ReadingController::class, 'listening'])->name('english.listening.download');
});
Route::prefix('writing')->group(function () {
    Route::get('download', [ReadingController::class, 'writing'])->name('english.writing.download');
});
