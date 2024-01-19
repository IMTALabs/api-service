<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $reading = \App\Models\ReadingRequest::with('user')->first();

    return view('english.reading', compact('reading'));

    // $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('english.reading', compact('reading'));
    // return $pdf->download('invoice.pdf');

    // return \Spatie\LaravelPdf\Facades\Pdf::view('english.reading', compact('reading'))
    //     ->withBrowsershot(function (Browsershot $browsershot) {
    //         $browsershot->setNodeBinary('/home/quanph/.nvm/versions/node/v18.18.0/bin/node')
    //             ->setNpmBinary('/home/quanph/.nvm/versions/node/v18.18.0/bin/npm')
    //             ->setChromePath('/var/www/html/api-service/chrome/linux/chrome-linux64/chrome');
    //     })
    //     ->format('a4')
    //     ->save('invoice.pdf');


});

Route::get('/test', function () {
    \Spatie\Browsershot\Browsershot::url('http://api.imta.edu/')
        // \Spatie\Browsershot\Browsershot::html('https://google.com/')
        ->setIncludePath('/home/quanph/.nvm/versions/node/v18.18.0/bin')
        ->newHeadless()
        ->save('invoice.pdf');
});
