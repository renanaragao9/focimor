<?php

declare(strict_types=1);

namespace Tests\Feature\Adoption;

use App\Models\Ong;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Ong\HasOngFixtures;
use Tests\TestCase;

class AdoptionByOngMemberTest extends TestCase
{
    use HasOngFixtures, RefreshDatabase;

    private Ong $ong;

    private User $adopter;

    private User $member;

    private function setUpOng(string $status = 'pending'): array
    {
        $this->loginAsTutor('adotante@example.com');
        $this->adopter = $this->tutor;
        $this->member = $this->makeUser('membro@example.com');
        $this->ong = $this->makeOng();
        $this->joinOng($this->ong, $this->member);
        $creator = $this->makeUser('criador@example.com');
        $pet = $this->makeAdoptionPetFor($creator, ['ong_id' => $this->ong->id]);

        return [$pet, $this->makeAdoption($this->adopter, $pet, $status)];
    }

    public function test_ong_member_sees_and_decides_interests_in_the_ong_pets(): void
    {
        [, $adoption] = $this->setUpOng();
        $this->switchTo($this->member);

        $this->getJson('/api/v1/adoptions/received')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.role', 'owner')->assertJsonPath('data.0.pet.ong.uuid', $this->ong->uuid);
        $this->getJson("/api/v1/adoptions/{$adoption->uuid}")->assertOk();
        $this->putJson("/api/v1/adoptions/{$adoption->uuid}/decision", ['status' => 'accepted'])->assertOk()->assertJsonPath('data.status', 'accepted');

        $this->assertSame($this->member->id, $adoption->fresh()->decided_by_user_id);
    }

    public function test_ong_member_can_chat_with_the_adopter_after_accepting(): void
    {
        [, $adoption] = $this->setUpOng('accepted');
        $this->switchTo($this->member);

        $this->postJson("/api/v1/adoptions/{$adoption->uuid}/messages", ['body' => 'Venha conhecer o Bidu'])->assertCreated();

        $this->switchTo($this->adopter);
        $this->getJson("/api/v1/adoptions/{$adoption->uuid}/messages")->assertJsonPath('data.0.body', 'Venha conhecer o Bidu')->assertJsonPath('data.0.is_mine', false);
    }

    public function test_non_members_of_the_ong_cannot_see_or_decide(): void
    {
        [, $adoption] = $this->setUpOng();
        $this->switchTo($this->makeUser('intruso@example.com'));

        $this->getJson('/api/v1/adoptions/received')->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/adoptions/{$adoption->uuid}")->assertNotFound();
        $this->putJson("/api/v1/adoptions/{$adoption->uuid}/decision", ['status' => 'accepted'])->assertNotFound();
    }

    public function test_a_member_that_left_the_ong_loses_access(): void
    {
        [, $adoption] = $this->setUpOng();
        $this->ong->users()->detach($this->member->id);
        $this->switchTo($this->member);

        $this->putJson("/api/v1/adoptions/{$adoption->uuid}/decision", ['status' => 'accepted'])->assertNotFound();
    }

    public function test_ong_members_cannot_adopt_pets_of_their_own_ong_and_do_not_see_them_in_the_feed(): void
    {
        [$pet] = $this->setUpOng();
        $this->switchTo($this->member);

        $this->getJson('/api/v1/adoptions/feed')->assertJsonCount(0, 'data');
        $this->postJson('/api/v1/adoptions/swipes', ['pet_uuid' => $pet->uuid, 'action' => 'like'])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Você não pode adotar um pet seu.');
    }

    public function test_the_adopter_still_sees_the_pet_in_the_feed_and_the_ong(): void
    {
        $this->loginAsTutor('adotante@example.com');
        $ong = $this->makeOng(['name' => 'Patas']);
        $this->makeAdoptionPetFor($this->makeUser('criador@example.com'), ['ong_id' => $ong->id]);

        $this->getJson('/api/v1/adoptions/feed')->assertJsonPath('data.0.ong.name', 'Patas');
    }
}
