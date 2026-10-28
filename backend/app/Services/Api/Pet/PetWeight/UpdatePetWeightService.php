<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetWeight;

use App\Models\PetWeight;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class UpdatePetWeightService extends BaseService
{
    public function run(PetWeight $weight, array $data): PetWeight
    {
        return DB::transaction(function () use ($weight, $data): PetWeight {
            $weight->update($data);

            return $weight->refresh();
        });
    }
}
