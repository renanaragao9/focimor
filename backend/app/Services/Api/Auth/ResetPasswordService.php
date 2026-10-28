<?php

declare(strict_types=1);

namespace App\Services\Api\Auth;

use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordService extends BaseService
{
    public function run(array $data): bool
    {
        $status = Password::reset($data, function (User $user, string $password): void {
            $user->forceFill(['password' => $password])->setRememberToken(Str::random(60));
            $user->save();
            $user->tokens()->delete();
        });

        return $status === Password::PASSWORD_RESET;
    }
}
