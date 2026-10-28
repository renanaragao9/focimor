<?php

declare(strict_types=1);

namespace App\Services\Api\Auth;

use App\Models\EmailVerificationCode;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class VerifyEmailService extends BaseService
{
    public const MAX_ATTEMPTS = 5;

    public function run(array $data): ?User
    {
        $email = $data['email'];
        $code = (string) $data['code'];

        return DB::transaction(function () use ($email, $code): ?User {
            $user = User::where('email', $email)->first();

            if (! $user || $user->email_verified_at) {
                return null;
            }

            $record = EmailVerificationCode::where('user_id', $user->id)->lockForUpdate()->first();

            if (! $record || $record->isExpired() || $record->isVerified() || $record->attempts >= self::MAX_ATTEMPTS) {
                return null;
            }

            if (! hash_equals($record->code, $code)) {
                $record->increment('attempts');

                return null;
            }

            $record->markAsVerified();
            $user->forceFill(['email_verified_at' => now()])->save();

            return $user->fresh();
        });
    }
}
