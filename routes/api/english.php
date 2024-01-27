<?php

/**
 * Rules:
 *
 * 1. Mõi group phải cách nhau 1 dòng trống
 * 2. Hard wrap ở 120 ký tự
 * 3. Route bắt buộc phải có tên và luôn bắt đầu bằng 'english.'
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\English\AuthController;
use App\Http\Controllers\Api\English\ReadingController;
use App\Http\Controllers\Api\English\ListeningController;
use App\Http\Controllers\Api\English\WritingController;

Route::prefix('auth')->group(function () {
    Route::post('get-access-token', [AuthController::class, 'getAccessToken'])->name('english.auth.get-access-token');
});

Route::prefix('/listening')->name('listening.')->group(function () {
    Route::post('', [ListeningController::class, 'listening'])->name('listening.submit');
    Route::get('/random_video', [ListeningController::class, 'randomListening'])->name('listening.random.video');
    Route::post('/mark', [ListeningController::class, 'submitListening'])->name('listening.mark');
    Route::get('/{hash}', [ListeningController::class, 'listeningTest'])->name('listening.test');
});

Route::prefix('reading')->group(function () {
    Route::post('/gen_topic', [ReadingController::class, 'genTopic'])->name('reading.genTopic');
    Route::post('', [ReadingController::class, 'reading'])->name('reading.reading');
    Route::post('/mark', [ReadingController::class, 'submitReading'])->name('reading.submitReading');
    Route::get('/{hash}', [ReadingController::class, 'readingTest'])->name('reading.readingTest');
    // Route::get('download', [ReadingController::class, 'download'])->name('english.reading.download');
});

Route::prefix('/writing')->group(function () {
    Route::post('/gen_instruction', [WritingController::class, 'writing_gen_instruction'])->name('english.writing.gen_instruction');
    Route::post('/evalue', [WritingController::class, 'evalue'])->name('english.writing.evalue');
    Route::post('/{hash}', [WritingController::class, 'writingTest'])->name('english.writing.hash');
    Route::get('/randomWriting', [WritingController::class, 'randomWriting'])->name('english.writing.random');
});

