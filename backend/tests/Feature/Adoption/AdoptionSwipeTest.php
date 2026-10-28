<?php

declare(strict_types=1);

namespace Tests\Feature\Adoption;

use App\Models\AdoptionMatch;
use App\Models\AdoptionSwipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdoptionSwipeTest extends TestCase
{
    use HasAdoptionFixtures, RefreshDatabase;

    private function swipe(string $petUuid, string $action = 'like', bool $direct = false)
    {
        return $this->postJson('/api/v1/adoptions/swipes', ['pet_uuid' => $petUuid, 'action' => $action, 'direct' => $direct]);
    }

    public function test_requires_authentication(): void
    {
        $this->postJson('/api/v1/adoptions/swipes', [])->assertUnauthorized();
    }

    public function test_like_creates_a_pending_interest_for_the_owner(): void
    {
        $user = $this->loginAsTutor();
        $pet = $this->makeAdoptionPetFor($this->makeUser('dono@example.com'));

        $this->swipe($pet->uuid)
            ->assertCreated()
            ->assertJsonPath('data.interested', true)
            ->assertJsonPath('data.adoption.status', 'pending')
            ->assertJsonPath('data.adoption.role', 'adopter')
            ->assertJsonPath('data.adoption.pet.name', 'Bidu')
            ->assertJsonPath('message', 'Interesse enviado ao dono do pet.');

        $this->assertDatabaseHas('adoption_swipes', ['user_id' => $user->id, 'pet_id' => $pet->id, 'action' => 'like']);
        $this->assertDatabaseHas('adoption_matches', ['user_id' => $user->id, 'pet_id' => $pet->id, 'status' => 'pending']);
    }

    public function test_superlike_also_creates_the_interest(): void
    {
        $this->loginAsTutor();
        $pet = $this->makeAdoptionPetFor($this->makeUser('dono@example.com'));

        $this->swipe($pet->uuid, 'superlike')->assertCreated()->assertJsonPath('data.interested', true);
        $this->assertSame(1, AdoptionMatch::count());
    }

    public function test_dislike_only_records_the_swipe(): void
    {
        $this->loginAsTutor();
        $pet = $this->makeAdoptionPetFor($this->makeUser('dono@example.com'));

        $this->swipe($pet->uuid, 'dislike')->assertCreated()->assertJsonPath('data.interested', false)->assertJsonPath('data.adoption', null);

        $this->assertSame(1, AdoptionSwipe::count());
        $this->assertSame(0, AdoptionMatch::count());
    }

    public function test_cannot_swipe_twice_on_the_same_pet(): void
    {
        $this->loginAsTutor();
        $pet = $this->makeAdoptionPetFor($this->makeUser('dono@example.com'));
        $this->swipe($pet->uuid, 'dislike')->assertCreated();

        $this->swipe($pet->uuid)->assertUnprocessable()->assertJsonPath('message', 'Você já curtiu ou passou neste pet.');

        $this->assertSame(0, AdoptionMatch::count());
    }

    public function test_direct_adopt_overrides_a_previous_pass(): void
    {
        $this->loginAsTutor();
        $pet = $this->makeAdoptionPetFor($this->makeUser('dono@example.com'));
        $this->swipe($pet->uuid, 'dislike')->assertCreated();

        $this->swipe($pet->uuid, 'like', true)->assertCreated()->assertJsonPath('data.interested', true)->assertJsonPath('data.adoption.status', 'pending');

        $this->assertSame(1, AdoptionSwipe::count());
        $this->assertSame('like', AdoptionSwipe::first()->action);
        $this->assertSame(1, AdoptionMatch::count());
    }

    public function test_direct_adopt_says_when_contact_was_already_made(): void
    {
        $this->loginAsTutor();
        $pet = $this->makeAdoptionPetFor($this->makeUser('dono@example.com'));
        $this->swipe($pet->uuid)->assertCreated();

        $this->swipe($pet->uuid, 'like', true)->assertUnprocessable()->assertJsonPath('message', 'Você já entrou em contato para adotar este pet. Acompanhe em Adoções.');

        $this->assertSame(1, AdoptionMatch::count());
    }

    public function test_direct_adopt_reopens_a_cancelled_interest_but_not_a_declined_one(): void
    {
        $this->loginAsTutor();
        $pet = $this->makeAdoptionPetFor($this->makeUser('dono@example.com'));
        $other = $this->makeAdoptionPetFor($this->makeUser('dono2@example.com'), ['name' => 'Outro']);
        $this->swipe($pet->uuid)->assertCreated();
        $this->swipe($other->uuid)->assertCreated();
        AdoptionMatch::where('pet_id', $pet->id)->update(['status' => 'cancelled']);
        AdoptionMatch::where('pet_id', $other->id)->update(['status' => 'declined']);

        $this->swipe($pet->uuid, 'like', true)->assertCreated()->assertJsonPath('data.adoption.status', 'pending');
        $this->swipe($other->uuid, 'like', true)->assertUnprocessable()->assertJsonPath('message', 'O responsável recusou o seu interesse neste pet.');

        $this->assertSame(2, AdoptionMatch::count());
    }

    public function test_cannot_adopt_own_pet_or_non_adoption_pet(): void
    {
        $user = $this->loginAsTutor();
        $mine = $this->makeAdoptionPetFor($user);
        $matchPet = $this->makePetFor($this->makeUser('dono@example.com'), ['name' => 'Pet de Match']);

        $this->swipe($mine->uuid)->assertUnprocessable()->assertJsonPath('message', 'Você não pode adotar um pet seu.');
        $this->swipe($matchPet->uuid)->assertUnprocessable()->assertJsonPath('message', 'Este pet não está disponível para adoção.');
        $this->assertSame(0, AdoptionSwipe::count());
    }

    public function test_cannot_swipe_on_inactive_pets(): void
    {
        $this->loginAsTutor();
        $pet = $this->makeAdoptionPetFor($this->makeUser('dono@example.com'), ['is_active' => false]);

        $this->swipe($pet->uuid)->assertUnprocessable()->assertJsonValidationErrors('pet_uuid', 'errors');
    }

    public function test_validates_payload(): void
    {
        $this->loginAsTutor();

        $this->postJson('/api/v1/adoptions/swipes', [])->assertUnprocessable()->assertJsonValidationErrors(['pet_uuid', 'action'], 'errors');
        $this->postJson('/api/v1/adoptions/swipes', ['pet_uuid' => 'x', 'action' => 'love'])->assertUnprocessable();
        $this->postJson('/api/v1/adoptions/swipes', ['pet_uuid' => '00000000-0000-0000-0000-000000000000', 'action' => 'like'])->assertUnprocessable();
    }
}
