<?php

namespace App\Services\English;

use App\Models\User;

class UserService
{
    public static function createNewUser(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'password' => $data['password'],
            'name' => fake(app()->getLocale())->name(),
            'avatar' => 'https://picsum.photos/200/300',
        ]);
    }
}
