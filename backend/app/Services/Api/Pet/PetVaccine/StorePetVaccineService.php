<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetVaccine;

use App\Models\Pet;
use App\Models\PetVaccine;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class StorePetVaccineService extends BaseService
{
    public function run(Pet $pet, array $data): PetVaccine
    {
        return DB::transaction(fn (): PetVaccine => $pet->vaccines()->create($data));
    }
}
