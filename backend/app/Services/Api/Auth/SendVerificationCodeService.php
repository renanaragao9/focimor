<?php

declare(strict_types=1);

namespace App\Services\Api\Auth;

use App\Jobs\SendEmailVerificationJob;
use App\Models\EmailVerificationCode;
use App\Models\User;
use App\Services\BaseService;

class SendVerificationCodeService extends BaseService
{
    public function run(User $user): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailVerificationCode::updateOrCreate(
            ['user_id' => $user->id],
            ['code' => $code, 'attempts' => 0, 'expires_at' => now()->addMinutes(15), 'verified_at' => null],
        );

        SendEmailVerificationJob::dispatch($user, $code);
    }
}
