<?php

namespace App\Http\Controllers\Api\English;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\English\LoginRequest;
use App\Http\Requests\Api\English\RegisterRequest;
use App\Http\Resources\English\UserResource;
use App\Services\English\UserService;
use App\Traits\APIResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    use APIResponse;

    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return $this->responseUnAuthenticated(__('Invalid credentials'));
        }

        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('english')->plainTextToken;

        return $this->responseSuccess(null, [
            'user' => new UserResource($user),
            'accessToken' => $token,
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $user = UserService::createNewUser($request->only(['email', 'password']));
        $user->tokens()->delete();
        $token = $user->createToken('english')->plainTextToken;

        return $this->responseSuccess(null, [
            'user' => new UserResource($user),
            'accessToken' => $token,
        ]);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        Session::flush();
        Session::regenerate();

        return $this->responseSuccess(__('Logged out successfully'));
    }

    public function user()
    {
        $user = Auth::user();

        return $this->responseSuccess(null, new UserResource($user));
    }
}
