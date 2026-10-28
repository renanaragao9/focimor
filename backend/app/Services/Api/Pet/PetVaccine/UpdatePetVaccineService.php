<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetVaccine;

use App\Models\PetVaccine;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class UpdatePetVaccineService extends BaseService
{
    public function run(PetVaccine $vaccine, array $data): PetVaccine
    {
        return DB::transaction(function () use ($vaccine, $data): PetVaccine {
            $vaccine->update($data);

            return $vaccine->refresh();
        });
    }
}
