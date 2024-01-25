<?php

namespace App\Http\Controllers\Api\English;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\English\GetAccessTokenRequest;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    use ApiResponse;

    public function getAccessToken(GetAccessTokenRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return $this->responseUnAuthenticated('Invalid credentials');
        }

        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('english')->plainTextToken;

        return $this->responseSuccess(null, [
            'token' => $token,
        ]);
    }
    
}
