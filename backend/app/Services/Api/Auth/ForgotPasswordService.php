<?php

declare(strict_types=1);

namespace App\Services\Api\Auth;

use App\Models\User;
use App\Notifications\Auth\PasswordResetNotification;
use App\Services\BaseService;
use Illuminate\Support\Facades\Password;

class ForgotPasswordService extends BaseService
{
    public function run(array $data): void
    {
        $email = $data['email'];

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->notify(new PasswordResetNotification(Password::createToken($user), $user->email));
        }
    }
}
