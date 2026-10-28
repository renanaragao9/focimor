<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetDetail;

use App\Models\Pet;
use App\Models\PetDetail;
use App\Services\BaseService;

class ViewPetDetailService extends BaseService
{
    public function run(Pet $pet): ?PetDetail
    {
        return $pet->detail()->first();
    }
}
