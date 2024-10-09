<?php

namespace App\Services;

use App\DTO\RegisterDto;
use App\Models\User;

class UserService
{
    public function createUser(RegisterDto $registerDto): User
    {
        $user = new User;

        $user->name = $registerDto->name;
        $user->email = $registerDto->email;
        $user->password = $registerDto->password;

        $user->save();

        return $user;
    }
}
