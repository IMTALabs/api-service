<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\English\AuthController;
use App\Http\Controllers\Api\English\ReadingController;
use App\Http\Controllers\Api\English\ListeningController;

Route::prefix('auth')->group(function () {
    Route::post('get-access-token', [AuthController::class, 'getAccessToken'])->name('english.auth.get-access-token');

    Route::prefix('reading')->group(function () {
        Route::get('download', [ReadingController::class, 'download'])->name('english.reading.download');
    });
});

Route::prefix('/listening')->name('listening.')->group(function () {
    Route::post('', [ListeningController::class, 'listening'])->name('listening.submit');
    Route::get('/random_video', [ListeningController::class, 'randomListening'])->name('listening.random.video');
    Route::post('/mark', [ListeningController::class, 'submitListening'])->name('listening.mark');
    Route::get('/{hash}', [ListeningController::class, 'listeningTest'])->name('listening.test');
});
