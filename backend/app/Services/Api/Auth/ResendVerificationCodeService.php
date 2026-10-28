<?php

declare(strict_types=1);

namespace App\Services\Api\Auth;

use App\Models\User;
use App\Services\BaseService;

class ResendVerificationCodeService extends BaseService
{
    public function __construct(private readonly SendVerificationCodeService $sendCode) {}

    public function run(array $data): void
    {
        $email = $data['email'];

        $user = User::where('email', $email)->first();

        if ($user && $user->email_verified_at === null) {
            $this->sendCode->run($user);
        }
    }
}
