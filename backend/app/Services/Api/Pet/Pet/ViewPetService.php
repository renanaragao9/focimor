<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\Pet;

use App\Models\Pet;
use App\Services\BaseService;

class ViewPetService extends BaseService
{
    public function run(Pet $pet): Pet
    {
        return $pet->load(['species', 'breed', 'size', 'goal', 'photos', 'detail']);
    }
}
