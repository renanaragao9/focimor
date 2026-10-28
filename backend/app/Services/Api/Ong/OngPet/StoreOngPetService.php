<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngPet;

use App\Models\Goal;
use App\Models\Ong;
use App\Models\Pet;
use App\Services\BaseService;
use App\Traits\AuthorizesOngActions;
use Illuminate\Support\Facades\DB;

class StoreOngPetService extends BaseService
{
    use AuthorizesOngActions;

    public function run(Ong $managedOng, array $data): Pet
    {
        $this->ensureOngVerified($managedOng);

        $userId = $this->getUserId();
        $goalId = Goal::where('slug', 'adoption')->value('id');

        return DB::transaction(function () use ($managedOng, $data, $userId, $goalId): Pet {
            $attributes = array_merge($data, [
                'user_id' => $userId,
                'ong_id' => $managedOng->id,
                'goal_id' => $goalId,
                'is_active' => true,
            ]);

            return Pet::create($attributes)->load(['species', 'breed', 'size', 'goal', 'photos']);
        });
    }
}
