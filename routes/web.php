<?php

use Illuminate\Support\Facades\Route;
use Spatie\LaravelPdf\Enums\Unit;

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

// Route::get('/', function () {

//     // dd(Process::run('PATH=$PATH:/usr/local/bin:/opt/homebrew/bin NODE_PATH=`/home/quanph/.nvm/versions/node/v18.19.0/bin/node /home/quanph/.nvm/versions/node/v18.19.0/bin/npm root -g` /home/quanph/.nvm/versions/node/v18.19.0/bin/node -v')->errorOutput());

//     $reading = \App\Models\ReadingRequest::with('user')->first();

//     return view('english.reading', compact('reading'));

//     $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('english.reading', compact('reading'));
//     return $pdf->download('invoice.pdf');

//     // return \Spatie\LaravelPdf\Facades\Pdf::view('english.reading', compact('reading'))
//     //     ->withBrowsershot(function (\Spatie\Browsershot\Browsershot $browsershot) {
//     //         $browsershot->setNodeBinary('/home/quanph/.nvm/versions/node/v18.19.0/bin/node')
//     //             ->setNpmBinary('/home/quanph/.nvm/versions/node/v18.19.0/bin/npm');
//     //     })
//     //     ->format('a4')
//     //     ->save('invoice.pdf');


// });

// Route::get('/test', function () {
//     $reading = \App\Models\ReadingRequest::with('user')->first();

//     // dump(Process::run('which node')->errorOutput());
//     // dd(Process::run('which node')->output());

//     // \Spatie\Browsershot\Browsershot::url('http://api.imta.edu/')
//     // // \Spatie\Browsershot\Browsershot::url('https://google.com/')
//     //     ->setIncludePath('/var/www/html/api-service/node/bin')
//     //     ->setChromePath('/var/www/html/api-service/chrome-linux/chrome')
//     //     ->noSandbox()
//     //     ->save('invoice.pdf');

//     return \Spatie\LaravelPdf\Facades\Pdf::view('english.reading', compact('reading'))
//         ->withBrowsershot(function (\Spatie\Browsershot\Browsershot $browsershot) {
//             $browsershot->setIncludePath('/var/www/html/api-service/node/bin')
//                 ->setChromePath('/var/www/html/api-service/chrome-linux/chrome')
//                 ->noSandbox();
//         })
//         ->format('a4')
//         ->margins(20, 0, 20, 0, Unit::Pixel)
//         ->save('test.pdf');
//});
