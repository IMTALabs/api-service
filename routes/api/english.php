<?php

use App\Http\Controllers\Api\English\AuthController;
use App\Http\Controllers\Api\English\ListeningController;
use App\Http\Controllers\Api\English\ReadingController;
use App\Http\Controllers\Api\English\WritingController;
use Illuminate\Support\Facades\Route;

/**
 * Rules:
 *
 * 1. Mõi group phải cách nhau 1 dòng trống
 * 2. Hard wrap ở 120 ký tự
 * 3. Route bắt buộc phải có tên và luôn bắt đầu bằng 'english.'
 */

Route::prefix('')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('english.auth.login');
    Route::post('register', [AuthController::class, 'register'])->name('english.auth.register');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('english.auth.logout');
        Route::get('info_user', [AuthController::class, 'user'])->name('english.auth.user');
        Route::post('get-access-token',
            [AuthController::class, 'getAccessToken'])->name('english.auth.get-access-token');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('listening')->group(function () {
        Route::post('', [ListeningController::class, 'listening'])->name('english.listening.generate')
            ->middleware('throttle:english.listening');
        Route::post('mark', [ListeningController::class, 'grading'])->name('english.listening.grading')
            ->middleware('throttle:english.listening');
        Route::get('random_video', [ListeningController::class, 'randomYoutubeVideos'])
            ->name('english.listening.random_youtube_videos');
        Route::get('{hash}', [ListeningController::class, 'listeningTest'])->name('english.listening.test');
    });

    Route::prefix('reading')->group(function () {
        Route::post('/gen_topic', [ReadingController::class, 'article'])->name('reading.genTopic');
        Route::post('', [ReadingController::class, 'reading'])->name('reading.reading');
        Route::post('/mark', [ReadingController::class, 'submitReading'])->name('reading.submitReading');
        Route::get('/{hash}', [ReadingController::class, 'readingTest'])->name('reading.readingTest');
        // Route::get('download', [ReadingController::class, 'download'])->name('english.reading.download');
    });

    Route::prefix('/writing')->group(function () {
        Route::post('/gen_instruction',
            [WritingController::class, 'writing_gen_instruction'])->name('english.writing.gen_instruction');
        Route::post('/evalue', [WritingController::class, 'evalue'])->name('english.writing.evalue');
        Route::post('/{hash}', [WritingController::class, 'writingTest'])->name('english.writing.hash');
        Route::get('/randomWriting', [WritingController::class, 'randomWriting'])->name('english.writing.random');
    });
});

