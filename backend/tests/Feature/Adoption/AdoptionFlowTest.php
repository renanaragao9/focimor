<?php

declare(strict_types=1);

namespace Tests\Feature\Adoption;

use App\Models\AdoptionMatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdoptionFlowTest extends TestCase
{
    use HasAdoptionFixtures, RefreshDatabase;

    private User $adopter;

    private User $owner;

    private function setUpAs(string $role, string $status = 'pending'): AdoptionMatch
    {
        $this->loginAsTutor('adotante@example.com');
        $this->adopter = $this->tutor;
        $this->owner = $this->makeUser('dono@example.com');
        $adoption = $this->makeAdoption($this->adopter, $this->makeAdoptionPetFor($this->owner), $status);

        if ($role === 'owner') {
            $this->switchTo($this->owner);
        }

        return $adoption;
    }

    public function test_requires_authentication(): void
    {
        $this->getJson('/api/v1/adoptions')->assertUnauthorized();
        $this->getJson('/api/v1/adoptions/received')->assertUnauthorized();
        $this->putJson('/api/v1/adoptions/'.fake()->uuid().'/decision', [])->assertUnauthorized();
        $this->deleteJson('/api/v1/adoptions/'.fake()->uuid())->assertUnauthorized();
    }

    public function test_adopter_lists_only_their_own_interests(): void
    {
        $this->setUpAs('adopter');
        $this->makeAdoption($this->makeUser('outro@example.com'), $this->makeAdoptionPetFor($this->owner, ['name' => 'Nina']));

        $response = $this->getJson('/api/v1/adoptions')->assertOk();

        $response->assertJsonCount(1, 'data')->assertJsonPath('data.0.role', 'adopter')->assertJsonPath('data.0.pet.name', 'Bidu')->assertJsonPath('data.0.status_label', 'Pendente');
        $response->assertJsonMissingPath('data.0.adopter');
    }

    public function test_adopter_list_filters_by_status(): void
    {
        $this->setUpAs('adopter');
        $this->makeAdoption($this->adopter, $this->makeAdoptionPetFor($this->owner, ['name' => 'Nina']), 'accepted');

        $this->getJson('/api/v1/adoptions?filters[status]=accepted')->assertJsonCount(1, 'data')->assertJsonPath('data.0.pet.name', 'Nina');
        $this->getJson('/api/v1/adoptions?filters[status]=invalid')->assertUnprocessable();
    }

    public function test_owner_lists_received_interests_with_adopter_name_only(): void
    {
        $this->setUpAs('owner');
        $this->makeAdoption($this->makeUser('terceiro@example.com'), $this->makeAdoptionPetFor($this->makeUser('outro-dono@example.com'), ['name' => 'Alheio']));

        $response = $this->getJson('/api/v1/adoptions/received')->assertOk();

        $response->assertJsonCount(1, 'data')->assertJsonPath('data.0.role', 'owner')->assertJsonPath('data.0.adopter.name', 'Tutor');
        $this->assertSame(['name'], array_keys($response->json('data.0.adopter')));
    }

    public function test_received_filters_by_pet_and_status(): void
    {
        $this->setUpAs('owner');
        $nina = $this->makeAdoptionPetFor($this->owner, ['name' => 'Nina']);
        $this->makeAdoption($this->adopter, $nina, 'accepted');

        $this->getJson("/api/v1/adoptions/received?filters[pet_uuid]={$nina->uuid}")->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/adoptions/received?filters[status]=accepted')->assertJsonCount(1, 'data')->assertJsonPath('data.0.pet.name', 'Nina');
        $this->getJson('/api/v1/adoptions/received?filters[status]=declined')->assertJsonCount(0, 'data');
    }

    public function test_show_is_available_to_both_parties_and_hidden_from_others(): void
    {
        $adoption = $this->setUpAs('adopter');
        $this->getJson("/api/v1/adoptions/{$adoption->uuid}")->assertOk()->assertJsonPath('data.role', 'adopter');

        $this->switchTo($this->owner);
        $this->getJson("/api/v1/adoptions/{$adoption->uuid}")->assertOk()->assertJsonPath('data.role', 'owner');

        $this->switchTo($this->makeUser('intruso@example.com'));
        $this->getJson("/api/v1/adoptions/{$adoption->uuid}")->assertNotFound();
    }

    public function test_owner_accepts_and_the_decision_is_recorded(): void
    {
        $adoption = $this->setUpAs('owner');

        $this->putJson("/api/v1/adoptions/{$adoption->uuid}/decision", ['status' => 'accepted'])
            ->assertOk()
            ->assertJsonPath('data.status', 'accepted')
            ->assertJsonPath('data.status_label', 'Aceito');

        $adoption->refresh();
        $this->assertNotNull($adoption->decided_at);
        $this->assertSame($this->owner->id, $adoption->decided_by_user_id);
    }

    public function test_adopted_deactivates_the_pet_and_declines_the_other_interests(): void
    {
        $adoption = $this->setUpAs('owner', 'accepted');
        $pet = $adoption->pet;
        $pending = $this->makeAdoption($this->makeUser('b@example.com'), $pet, 'pending');
        $accepted = $this->makeAdoption($this->makeUser('c@example.com'), $pet, 'accepted');
        $cancelled = $this->makeAdoption($this->makeUser('d@example.com'), $pet, 'cancelled');
        $otherPet = $this->makeAdoption($this->makeUser('e@example.com'), $this->makeAdoptionPetFor($this->owner, ['name' => 'Nina']), 'pending');

        $this->putJson("/api/v1/adoptions/{$adoption->uuid}/decision", ['status' => 'adopted'])->assertOk()->assertJsonPath('data.status', 'adopted');

        $this->assertFalse($pet->fresh()->is_active);
        $this->assertSame('declined', $pending->fresh()->status);
        $this->assertSame('declined', $accepted->fresh()->status);
        $this->assertSame('cancelled', $cancelled->fresh()->status);
        $this->assertSame('pending', $otherPet->fresh()->status);
    }

    #[DataProvider('invalidTransitions')]
    public function test_invalid_transitions_are_rejected(string $from, string $to): void
    {
        $adoption = $this->setUpAs('owner', $from);

        $this->putJson("/api/v1/adoptions/{$adoption->uuid}/decision", ['status' => $to])->assertUnprocessable()->assertJsonValidationErrors('status', 'errors');

        $this->assertSame($from, $adoption->fresh()->status);
    }

    public static function invalidTransitions(): array
    {
        return [
            'pendente para adotado' => ['pending', 'adopted'],
            'recusado para aceito' => ['declined', 'accepted'],
            'adotado para recusado' => ['adopted', 'declined'],
            'cancelado para aceito' => ['cancelled', 'accepted'],
            'aceito para aceito' => ['accepted', 'accepted'],
        ];
    }

    public function test_only_the_owner_can_decide(): void
    {
        $adoption = $this->setUpAs('adopter');

        $this->putJson("/api/v1/adoptions/{$adoption->uuid}/decision", ['status' => 'accepted'])->assertForbidden();

        $this->assertSame('pending', $adoption->fresh()->status);
    }

    public function test_decision_validates_status_value(): void
    {
        $adoption = $this->setUpAs('owner');

        $this->putJson("/api/v1/adoptions/{$adoption->uuid}/decision", [])->assertUnprocessable();
        $this->putJson("/api/v1/adoptions/{$adoption->uuid}/decision", ['status' => 'cancelled'])->assertUnprocessable();
        $this->putJson("/api/v1/adoptions/{$adoption->uuid}/decision", ['status' => 'pending'])->assertUnprocessable();
    }

    #[DataProvider('cancellableStatuses')]
    public function test_adopter_cancels_a_pending_or_accepted_interest(string $status): void
    {
        $adoption = $this->setUpAs('adopter', $status);

        $this->deleteJson("/api/v1/adoptions/{$adoption->uuid}")->assertOk()->assertJsonPath('data.status', 'cancelled');

        $this->assertSame('cancelled', $adoption->fresh()->status);
    }

    public static function cancellableStatuses(): array
    {
        return [['pending'], ['accepted']];
    }

    public function test_cannot_cancel_a_finished_interest(): void
    {
        $adoption = $this->setUpAs('adopter', 'adopted');

        $this->deleteJson("/api/v1/adoptions/{$adoption->uuid}")->assertUnprocessable();
    }

    public function test_only_the_adopter_can_cancel(): void
    {
        $adoption = $this->setUpAs('owner');

        $this->deleteJson("/api/v1/adoptions/{$adoption->uuid}")->assertForbidden();

        $this->assertSame('pending', $adoption->fresh()->status);
    }

    public function test_third_parties_cannot_decide_or_cancel(): void
    {
        $adoption = $this->setUpAs('adopter');
        $this->switchTo($this->makeUser('intruso@example.com'));

        $this->putJson("/api/v1/adoptions/{$adoption->uuid}/decision", ['status' => 'accepted'])->assertNotFound();
        $this->deleteJson("/api/v1/adoptions/{$adoption->uuid}")->assertNotFound();
    }

    public function test_full_flow_swipe_accept_chat_and_adopt(): void
    {
        $adopter = $this->loginAsTutor('adotante@example.com');
        $owner = $this->makeUser('dono@example.com');
        $pet = $this->makeAdoptionPetFor($owner);

        $this->getJson('/api/v1/adoptions/feed')->assertJsonPath('data.0.uuid', $pet->uuid);
        $uuid = $this->postJson('/api/v1/adoptions/swipes', ['pet_uuid' => $pet->uuid, 'action' => 'like'])->json('data.adoption.uuid');
        $this->getJson('/api/v1/adoptions/feed')->assertJsonCount(0, 'data');

        $this->switchTo($owner);
        $this->getJson('/api/v1/adoptions/received')->assertJsonPath('data.0.uuid', $uuid);
        $this->putJson("/api/v1/adoptions/{$uuid}/decision", ['status' => 'accepted'])->assertOk();
        $this->postJson("/api/v1/adoptions/{$uuid}/messages", ['body' => 'Pode vir conhecer o Bidu!'])->assertCreated();

        $this->switchTo($adopter);
        $this->getJson("/api/v1/adoptions/{$uuid}/messages")->assertJsonPath('data.0.body', 'Pode vir conhecer o Bidu!')->assertJsonPath('data.0.is_mine', false);

        $this->switchTo($owner);
        $this->putJson("/api/v1/adoptions/{$uuid}/decision", ['status' => 'adopted'])->assertOk();
        $this->assertFalse($pet->fresh()->is_active);
    }
}
