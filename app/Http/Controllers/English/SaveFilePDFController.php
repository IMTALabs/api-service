<?php

namespace App\Http\Controllers\English;

use App\Http\Controllers\Controller;
use App\Models\English\RequestReading;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;

class SaveFilePDFController extends Controller
{
    const PATH_VIEW = 'English.';

    public function reading(Request $request)
    {
        try {
            $hash = $request->query('hash');
            $getReading = RequestReading::query()->where('hash', $hash)->first();
            $paragraph = $getReading['paragraph'];
//        dd($paragraph);
            $converter = new CommonMarkConverter([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]);
            $paragraph = trim($paragraph, '"');
            $paragraph = Str::replace('\n', "\n", $paragraph);
            $convertedParagraph = $converter->convert($paragraph);
//        dd($convertedParagraph);
            $quiz = collect(json_decode($getReading['response'])->form)->map(function ($value) {
                return [
                    'question' => $value->question,
                    'choices' => $value->choices,
                ];
            });
            Browsershot::html('<h1>Hello world!!</h1>')->save('example.pdf');
//            $pdf = Pdf::view(self::PATH_VIEW . __FUNCTION__,
//                compact(['convertedParagraph', 'quiz']))
//                ->save(public_path('reading/' . $getReading['topic'] . '.pdf'));
//
////        return view(self::PATH_VIEW.__FUNCTION__,compact(['convertedParagraph','quiz']));
//            return $pdf->stream('invoice.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'errors'=>$e->getMessage(),
                'status'=>500
            ],500);
        }
    }
}
