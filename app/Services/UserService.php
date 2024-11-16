<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class UserService
{
    public function updateInfo(User $user, array $data): void
    {
        $user->update($data);
        // TODO: check if email changed - send verification email
    }
}
