<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetWeight;

use App\Models\Pet;
use App\Models\PetWeight;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class StorePetWeightService extends BaseService
{
    public function run(Pet $pet, array $data): PetWeight
    {
        return DB::transaction(fn (): PetWeight => $pet->weights()->create($data));
    }
}
