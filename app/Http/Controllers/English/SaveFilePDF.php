<?php

namespace App\Http\Controllers\English;

use App\Http\Controllers\Controller;
use App\Models\English\RequestReading;
use Illuminate\Http\Request;

class SaveFilePDF extends Controller
{
    public function saveFilePDF($hash){
//        $hash=$request->query('hash');
        $getReading=RequestReading::where('hash',$hash)->fist;
        dd($getReading);
    }
}
