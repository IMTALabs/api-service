<?php

use App\Http\Controllers\Api\English\AuthController;
use App\Http\Controllers\Api\English\ReadingController;
use App\Http\Controllers\Api\English\WritingController;

Route::prefix('auth')->group(function () {
    Route::post('get-access-token', [AuthController::class, 'getAccessToken'])->name('english.auth.get-access-token');

    Route::prefix('reading')->group(function () {
        Route::get('download', [ReadingController::class, 'download'])->name('english.reading.download');
    });
    

});

Route::prefix('/writing')->group(function () {
    Route::post('/gen_instruction', [WritingController::class, 'writing_gen_instruction'])->name('english.writing.gen_instruction');
    Route::post('/evalue', [WritingController::class, 'evalue'])->name('english.writing.evalue');
    Route::post('/{hash}', [WritingController::class, 'writingTest'])->name('english.writing.hash');
    Route::get('/randomWriting', [WritingController::class, 'randomWriting'])->name('english.writing.random');
});