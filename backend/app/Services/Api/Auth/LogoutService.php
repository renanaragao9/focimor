<?php

declare(strict_types=1);

namespace App\Services\Api\Auth;

use App\Services\BaseService;
use Laravel\Sanctum\PersonalAccessToken;

class LogoutService extends BaseService
{
    public function run(bool $allDevices = false): void
    {
        $user = $this->getUser();

        if ($allDevices) {
            $user->tokens()->delete();

            return;
        }

        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }
    }
}
