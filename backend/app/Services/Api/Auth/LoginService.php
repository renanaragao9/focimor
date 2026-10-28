<?php

declare(strict_types=1);

namespace App\Services\Api\Auth;

use App\Models\User;
use App\Services\BaseService;

class LoginService extends BaseService
{
    public function run(array $data): array
    {
        $email = $data['email'];

        $user = User::where('email', $email)->firstOrFail();

        return ['user' => $user, 'token' => $user->createToken('auth_token')->plainTextToken];
    }
}
