<?php

declare(strict_types=1);

namespace App\Services\Api\PetMatch\PetMatch;

use App\Models\PetMatch;
use App\Services\BaseService;

class ViewPetMatchService extends BaseService
{
    public function run(PetMatch $petMatch): PetMatch
    {
        $userId = $this->getUserId();

        return $petMatch
            ->load(['petOne.species', 'petOne.breed', 'petOne.size', 'petOne.photos', 'petTwo.species', 'petTwo.breed', 'petTwo.size', 'petTwo.photos'])
            ->loadCount(['messages as unread_count' => fn ($q) => $q->whereNull('read_at')->where('sender_user_id', '!=', $userId)]);
    }
}
