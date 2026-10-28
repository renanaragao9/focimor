<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetVaccine;

use App\Models\PetVaccine;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class DeletePetVaccineService extends BaseService
{
    public function run(PetVaccine $vaccine): void
    {
        DB::transaction(fn () => $vaccine->delete());
    }
}
