<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\English\AuthController;
use App\Http\Controllers\Api\English\ReadingController;

Route::prefix('auth')->group(function () {
    Route::post('get-access-token', [AuthController::class, 'getAccessToken'])->name('english.auth.get-access-token');


});

Route::prefix('reading')->group(function () {
    Route::post('/gen_topic', [ReadingController::class, 'genTopic'])->name('reading.genTopic');
    Route::post('', [ReadingController::class, 'reading'])->name('reading.reading');
    Route::post('/mark', [ReadingController::class, 'submitReading'])->name('reading.submitReading');
    Route::get('/{hash}', [ReadingController::class, 'readingTest'])->name('reading.readingTest');
    // Route::get('download', [ReadingController::class, 'download'])->name('english.reading.download');
});



