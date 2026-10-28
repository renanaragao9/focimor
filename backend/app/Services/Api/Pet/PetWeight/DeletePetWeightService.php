<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetWeight;

use App\Models\PetWeight;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class DeletePetWeightService extends BaseService
{
    public function run(PetWeight $weight): void
    {
        DB::transaction(fn () => $weight->delete());
    }
}
