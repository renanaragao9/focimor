<?php

declare(strict_types=1);

namespace App\Services\Api\Profile\Profile;

use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class UpdateProfilePasswordService extends BaseService
{
    public function run(array $data): void
    {
        $password = $data['password'];

        DB::transaction(function () use ($password): void {
            $user = $this->getUser();
            $current = $user->currentAccessToken();

            $user->update(['password' => $password]);

            $user->tokens()
                ->when($current instanceof PersonalAccessToken, fn ($q) => $q->where('id', '!=', $current->id))
                ->delete();
        });
    }
}
