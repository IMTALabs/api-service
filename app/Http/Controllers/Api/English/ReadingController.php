<?php

namespace App\Http\Controllers\Api\English;

use App\Http\Controllers\Controller;
use App\Models\English\RequestReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\CommonMark\CommonMarkConverter;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Unit;

class ReadingController extends Controller
{
    // CONST PATH_NODE = config('english.path_node');
    public function download(Request $request)
    {
        try {

            $hash = $request->query('hash');
            $reading = \App\Models\ReadingRequest::with('user')->where('hash', $hash)->first();
            $pdfPath = $hash . '.pdf';
            // dd($pdfPath);
            $reading->export_pdf = $pdfPath;
            $reading->save();
            $title = 'Reading test';
            return \Spatie\LaravelPdf\Facades\Pdf::view('english.reading', compact(['reading','title']))
                ->withBrowsershot(function (\Spatie\Browsershot\Browsershot $browsershot) {
                    $browsershot->setIncludePath('C:/Users/HI/OneDrive/Desktop/CongViec/api-service/node/bin')
                        ->setChromePath('C:/Users/HI/OneDrive/Desktop/CongViec/api-service/chrome-win/chrome')
                        ->noSandbox();
                })
                ->format('a4')
                ->margins(20, 0, 20, 0, Unit::Pixel)
                ->download($pdfPath);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
    public function listening(Request $request)
    {
        // try {

        $hash = $request->query('hash');

        $reading = DB::table('listening_request')
            ->join('users', 'users.id', '=', 'listening_request.user_id')
            ->select('listening_request.*', 'users.full_name', 'users.email')
            ->first();
        // dd($reading);
        $data = json_decode($reading->response, true);
        $pdfPath = $hash . '.pdf';
        $title = 'Listening test';
        // dd($pdfPath);
        // $reading->export_pdf = $pdfPath;
        // $reading->save();
        return \Spatie\LaravelPdf\Facades\Pdf::view('english.listening', compact(['reading', 'data', 'title']))
            ->withBrowsershot(function (\Spatie\Browsershot\Browsershot $browsershot) {
                $browsershot->setIncludePath('C:/Users/HI/OneDrive/Desktop/CongViec/api-service/node/bin')
                    ->setChromePath('C:/Users/HI/OneDrive/Desktop/CongViec/api-service/chrome-win/chrome')
                    ->noSandbox();
            })
            ->format('a4')
            ->margins(20, 0, 20, 0, Unit::Pixel)
            ->download($pdfPath);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'errors' => $e->getMessage(),
        //         'status' => 500
        //     ], 500);
        // }
    }
    public function writing(Request $request)
    {
        try {

            $hash = $request->query('hash');

            $reading = DB::table('writing_request')
                ->join('users', 'users.id', '=', 'writing_request.user_id')
                ->select('writing_request.*', 'users.full_name', 'users.email')
                ->first();
            // dd($reading);
            $pdfPath = $hash . '.pdf';
            // dd($pdfPath);    
            // $reading->export_pdf = $pdfPath;
            // $reading->save();
            $title = 'Writing test';
            // return view('english.writing', compact(['reading', 'title']));
            return \Spatie\LaravelPdf\Facades\Pdf::view('english.writing', compact(['reading', 'title']))
                ->withBrowsershot(function (\Spatie\Browsershot\Browsershot $browsershot) {
                    $browsershot->setIncludePath('/var/www/html/api-service/node/bin')
                        ->setChromePath('/var/www/html/api-service/chrome-linux/chrome')
                        ->noSandbox();
                })
                ->format('a4')
                ->margins(20, 0, 20, 0, Unit::Pixel)
                ->download($pdfPath);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
}
