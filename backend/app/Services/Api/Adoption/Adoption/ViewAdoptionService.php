<?php

declare(strict_types=1);

namespace App\Services\Api\Adoption\Adoption;

use App\Models\AdoptionMatch;
use App\Services\BaseService;

class ViewAdoptionService extends BaseService
{
    public function run(AdoptionMatch $adoptionMatch): AdoptionMatch
    {
        $userId = $this->getUserId();

        return $adoptionMatch
            ->load(['pet.species', 'pet.breed', 'pet.size', 'pet.goal', 'pet.photos', 'pet.ong', 'user', 'latestMessage'])
            ->loadCount(['messages as unread_count' => fn ($q) => $q->whereNull('read_at')->where('sender_user_id', '!=', $userId)]);
    }
}
