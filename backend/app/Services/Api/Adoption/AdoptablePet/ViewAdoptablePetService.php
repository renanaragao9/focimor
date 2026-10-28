<?php

declare(strict_types=1);

namespace App\Services\Api\Adoption\AdoptablePet;

use App\Models\Pet;
use App\Services\BaseService;

class ViewAdoptablePetService extends BaseService
{
    public function run(Pet $adoptablePet): Pet
    {
        return $adoptablePet->load(['species', 'breed', 'size', 'goal', 'photos', 'ong']);
    }
}
