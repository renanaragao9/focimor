<?php

declare(strict_types=1);

namespace App\Services\Api\Profile\Profile;

use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteProfileAvatarService extends BaseService
{
    public function run(): User
    {
        return DB::transaction(function (): User {
            $user = $this->getUser();
            $previous = $user->avatar_path;

            $user->update(['avatar_path' => null]);

            if ($previous) {
                Storage::disk(config('filesystems.media'))->delete($previous);
            }

            return $user->refresh();
        });
    }
}
