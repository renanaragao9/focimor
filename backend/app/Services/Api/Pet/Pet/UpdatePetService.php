<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\Pet;

use App\Models\Pet;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class UpdatePetService extends BaseService
{
    public function run(Pet $pet, array $data): Pet
    {
        return DB::transaction(function () use ($pet, $data): Pet {
            $pet->update($data);

            return $pet->refresh()->load(['species', 'breed', 'size', 'goal', 'photos']);
        });
    }
}
