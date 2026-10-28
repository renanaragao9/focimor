<?php

declare(strict_types=1);

namespace Tests\Feature\Adoption;

use App\Models\AdoptionMatch;
use App\Models\Goal;
use App\Models\Message;
use App\Models\Pet;
use App\Models\User;
use Tests\Feature\Pet\ActsAsTutor;

trait HasAdoptionFixtures
{
    use ActsAsTutor;

    protected function makeAdoptionPetFor(User $owner, array $overrides = []): Pet
    {
        return $this->makePetFor($owner, array_merge(['name' => 'Bidu', 'goal_id' => Goal::where('slug', 'adoption')->value('id')], $overrides));
    }

    protected function makeAdoption(User $adopter, Pet $pet, string $status = 'pending'): AdoptionMatch
    {
        return AdoptionMatch::create(['user_id' => $adopter->id, 'pet_id' => $pet->id, 'status' => $status]);
    }

    protected function sayInAdoption(User $sender, AdoptionMatch $adoption, string $body, array $extra = []): Message
    {
        return Message::create(array_merge(['body' => $body, 'adoption_match_id' => $adoption->id, 'sender_user_id' => $sender->id], $extra));
    }
}
