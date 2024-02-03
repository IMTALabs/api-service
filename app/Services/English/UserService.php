<?php

namespace App\Services\English;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserService
{
    public static function createNewUser(array $data)
    {
        $user = User::create([
            'email' => $data['email'],
            'password' => $data['password'],
            'name' => fake(app()->getLocale())->name(),
            'avatar' => 'https://picsum.photos/200/300',
        ]);

        try {
            $user->deposit(20);
        } catch (\Throwable $e) {
            Log::channel('server_error')->error($e->getMessage(), $e->getTrace());
        }

        return $user;
    }
}
