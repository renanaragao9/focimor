<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetDetail;

use App\Models\Pet;
use App\Models\PetDetail;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class UpdatePetDetailService extends BaseService
{
    public function run(Pet $pet, array $data): PetDetail
    {
        return DB::transaction(fn (): PetDetail => $pet->detail()->updateOrCreate([], $data));
    }
}
