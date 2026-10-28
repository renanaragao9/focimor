<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\Pet;

use App\Models\Pet;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class StorePetService extends BaseService
{
    public function run(array $data): Pet
    {
        $latitude = $data['latitude'] ?? null;
        $longitude = $data['longitude'] ?? null;

        return DB::transaction(function () use ($data, $latitude, $longitude): Pet {
            $user = $this->getUser();
            $hasLocation = $latitude !== null || $longitude !== null;

            $attributes = array_merge($data, [
                'user_id' => $user->id,
                'latitude' => $hasLocation ? $latitude : $user->latitude,
                'longitude' => $hasLocation ? $longitude : $user->longitude,
            ]);

            return Pet::create($attributes)->load(['species', 'breed', 'size', 'goal', 'photos']);
        });
    }
}
