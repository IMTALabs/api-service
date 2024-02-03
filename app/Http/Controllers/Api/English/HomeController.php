<?php

namespace App\Http\Controllers\Api\English;

use App\Http\Controllers\Controller;
use App\Models\EnglishRequest;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    use APIResponse;

    public function history(Request $request)
    {
        $number = $request->query('number', config('english.history.default_limit'));

        $englishRequests = EnglishRequest::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->limit($number)
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'skill' => Str::ucfirst($request->skill),
                    'created_at' => $request->created_at->diffForHumans(),
                ];
            });

        return $this->responseSuccess(null, ['data' => $englishRequests]);
    }
}
