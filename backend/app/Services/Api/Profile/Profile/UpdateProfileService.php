<?php

declare(strict_types=1);

namespace App\Services\Api\Profile\Profile;

use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class UpdateProfileService extends BaseService
{
    public function run(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = $this->getUser();
            $user->update($data);

            return $user->refresh();
        });
    }
}
