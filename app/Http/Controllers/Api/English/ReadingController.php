<?php

namespace App\Http\Controllers\Api\English;

use App\Http\Controllers\Controller;
use App\Models\English\ReadingRequest;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Unit;
use Spatie\LaravelPdf\Facades\Pdf;
use App\Traits\SlackNotifiable;

class ReadingController extends Controller
{
    use ApiResponse, SlackNotifiable;

    public function download(Request $request)
    {
        try {
            $hash = $request->query('hash');
            $reading = ReadingRequest::with('user')->where('hash', $hash)->first();
            if (!$reading) {
                $this->slackNotify('Reading test ' . $hash . ' not found', 'backend');
                return $this->responseNotFound(__('Reading test not found'));
            }

            $title = __('Reading test');
            return Pdf::view('english.reading', compact(['reading', 'title']))
                ->withBrowsershot(function (Browsershot $browsershot) {
                    $browsershot->setIncludePath('C:/Users/HI/OneDrive/Desktop/CongViec/api-service/node/bin')
                        ->setChromePath('C:/Users/HI/OneDrive/Desktop/CongViec/api-service/chrome-win/chrome')
                        ->noSandbox();
                })
                ->format('a4')
                ->margins(20, 0, 20, 0, Unit::Pixel)
                ->download($hash . '.pdf');
        } catch (\Exception $e) {
            $this->slackNotify($e->getMessage(), 'backend');
            return $this->responseServerError($e->getMessage(), $e->getMessage());
        }
    }

    public function listening(Request $request)
    {
        try {
            $hash = $request->query('hash');
            $reading = DB::table('listening_request')
                ->join('users', 'users.id', '=', 'listening_request.user_id')
                ->where('listening_request.hash', $hash)
                ->select('listening_request.*', 'users.full_name', 'users.email')
                ->first();
            if (!$reading) {
                return $this->responseNotFound(__('Listening test not found'));
            }

            $data = json_decode($reading->response, true);
            $pdfPath = $hash . '.pdf';
            $title = __('Listening test');
            return Pdf::view('english.listening', compact(['reading', 'data', 'title']))
                ->withBrowsershot(function (Browsershot $browsershot) {
                    $browsershot->setIncludePath('C:/Users/HI/OneDrive/Desktop/CongViec/api-service/node/bin')
                        ->setChromePath('C:/Users/HI/OneDrive/Desktop/CongViec/api-service/chrome-win/chrome')
                        ->noSandbox();
                })
                ->format('a4')
                ->margins(20, 0, 20, 0, Unit::Pixel)
                ->download($pdfPath);
        } catch (\Exception $e) {
            return $this->responseServerError($e->getMessage(), $e->getMessage());
        }
    }

    public function writing(Request $request)
    {
        try {

            $hash = $request->query('hash');
            $reading = DB::table('writing_request')
                ->join('users', 'users.id', '=', 'writing_request.user_id')
                ->where('writing_request.hash', $hash)
                ->select('writing_request.*', 'users.full_name', 'users.email')
                ->first();

            $pdfPath = $hash . '.pdf';
            $title = __('Writing test');
            return Pdf::view('english.writing', compact(['reading', 'title']))
                ->withBrowsershot(function (Browsershot $browsershot) {
                    $browsershot->setIncludePath('/var/www/html/api-service/node/bin')
                        ->setChromePath('/var/www/html/api-service/chrome-linux/chrome')
                        ->noSandbox();
                })
                ->format('a4')
                ->margins(20, 0, 20, 0, Unit::Pixel)
                ->download($pdfPath);
        } catch (\Exception $e) {
            return $this->responseServerError($e->getMessage(), $e->getMessage());
        }
    }
}
