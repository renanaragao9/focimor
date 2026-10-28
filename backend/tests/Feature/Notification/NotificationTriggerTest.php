<?php

declare(strict_types=1);

namespace Tests\Feature\Notification;

use App\Models\PetMatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Ong\HasOngFixtures;
use Tests\TestCase;

class NotificationTriggerTest extends TestCase
{
    use HasOngFixtures, RefreshDatabase;

    private function types(User $user): array
    {
        return $user->notifications()->get()->map(fn ($n) => $n->data['type'])->all();
    }

    private function swipe(string $petUuid, string $targetUuid, string $action = 'like')
    {
        return $this->postJson("/api/v1/pets/{$petUuid}/swipes", ['target_pet_uuid' => $targetUuid, 'action' => $action]);
    }

    public function test_a_mutual_like_notifies_only_the_other_owner(): void
    {
        $alice = $this->loginAsTutor('alice@example.com');
        $bob = $this->makeUser('bob@example.com');
        $rex = $this->makePetFor($alice, ['name' => 'Rex']);
        $luna = $this->makePetFor($bob, ['name' => 'Luna']);

        $this->swipe($rex->uuid, $luna->uuid);
        $this->assertSame([], $this->types($bob));

        $this->switchTo($bob);
        $this->swipe($luna->uuid, $rex->uuid)->assertJsonPath('data.matched', true);

        $notification = $alice->notifications()->firstOrFail();
        $this->assertSame('new_match', $notification->data['type']);
        $this->assertSame('Rex e Luna deram match.', $notification->data['body']);
        $this->assertSame(PetMatch::firstOrFail()->uuid, $notification->data['match_uuid']);
        $this->assertSame([], $this->types($bob));
    }

    public function test_one_sided_like_and_dislike_do_not_notify(): void
    {
        $alice = $this->loginAsTutor('alice@example.com');
        $bob = $this->makeUser('bob@example.com');
        $rex = $this->makePetFor($alice);
        $luna = $this->makePetFor($bob, ['name' => 'Luna']);

        $this->swipe($rex->uuid, $luna->uuid, 'dislike');

        $this->assertSame([], $this->types($alice));
        $this->assertSame([], $this->types($bob));
    }

    public function test_a_pet_match_message_notifies_the_other_owner_once_per_unread_burst(): void
    {
        $alice = $this->loginAsTutor('alice@example.com');
        $bob = $this->makeUser('bob@example.com');
        $match = PetMatch::create(['pet_one_id' => $this->makePetFor($alice, ['name' => 'Rex'])->id, 'pet_two_id' => $this->makePetFor($bob, ['name' => 'Luna'])->id, 'is_active' => true, 'matched_at' => now()]);
        $url = "/api/v1/matches/{$match->uuid}/messages";

        $this->postJson($url, ['body' => 'oi'])->assertCreated();
        $this->postJson($url, ['body' => 'tudo bem?'])->assertCreated();
        $this->postJson($url, ['body' => 'vamos passear?'])->assertCreated();

        $this->assertSame(1, $bob->notifications()->count());
        $this->assertSame(0, $alice->notifications()->count());
        $notification = $bob->notifications()->firstOrFail();
        $this->assertSame('new_message', $notification->data['type']);
        $this->assertSame('pet_match', $notification->data['kind']);
        $this->assertSame('Rex enviou uma mensagem.', $notification->data['body']);
        $this->assertSame($match->uuid, $notification->data['match_uuid']);
    }

    public function test_after_the_recipient_reads_a_new_message_notifies_again(): void
    {
        $alice = $this->loginAsTutor('alice@example.com');
        $bob = $this->makeUser('bob@example.com');
        $match = PetMatch::create(['pet_one_id' => $this->makePetFor($alice, ['name' => 'Rex'])->id, 'pet_two_id' => $this->makePetFor($bob, ['name' => 'Luna'])->id, 'is_active' => true, 'matched_at' => now()]);
        $url = "/api/v1/matches/{$match->uuid}/messages";
        $this->postJson($url, ['body' => 'oi'])->assertCreated();

        $this->switchTo($bob);
        $this->putJson("{$url}/read")->assertOk();
        $this->switchTo($alice);
        $this->postJson($url, ['body' => 'e aí?'])->assertCreated();

        $this->assertSame(2, $bob->notifications()->count());
    }

    public function test_an_adoption_interest_notifies_the_owner_and_all_ong_members_but_not_the_adopter(): void
    {
        $adopter = $this->loginAsTutor('adotante@example.com');
        $creator = $this->makeUser('criador@example.com');
        $member = $this->makeUser('membro@example.com');
        $outsider = $this->makeUser('fora@example.com');
        $ong = $this->makeOng();
        $this->joinOng($ong, $creator, 'admin');
        $this->joinOng($ong, $member);
        $pet = $this->makeAdoptionPetFor($creator, ['name' => 'Bidu', 'ong_id' => $ong->id]);

        $this->postJson('/api/v1/adoptions/swipes', ['pet_uuid' => $pet->uuid, 'action' => 'like'])->assertCreated();

        foreach ([$creator, $member] as $manager) {
            $notification = $manager->notifications()->firstOrFail();
            $this->assertSame('adoption_interest', $notification->data['type']);
            $this->assertSame('Novo interesse em Bidu', $notification->data['title']);
            $this->assertSame('Tutor quer adotar Bidu.', $notification->data['body']);
        }
        $this->assertSame(0, $outsider->notifications()->count());
        $this->assertSame(0, $adopter->notifications()->count());
    }

    public function test_an_adoption_interest_notifies_a_personal_owner(): void
    {
        $this->loginAsTutor('adotante@example.com');
        $owner = $this->makeUser('dono@example.com');
        $pet = $this->makeAdoptionPetFor($owner);

        $this->postJson('/api/v1/adoptions/swipes', ['pet_uuid' => $pet->uuid, 'action' => 'superlike'])->assertCreated();

        $this->assertSame(['adoption_interest'], $this->types($owner));
    }

    public function test_dislike_on_an_adoption_pet_does_not_notify(): void
    {
        $this->loginAsTutor('adotante@example.com');
        $owner = $this->makeUser('dono@example.com');
        $pet = $this->makeAdoptionPetFor($owner);

        $this->postJson('/api/v1/adoptions/swipes', ['pet_uuid' => $pet->uuid, 'action' => 'dislike'])->assertCreated();

        $this->assertSame(0, $owner->notifications()->count());
    }

    public function test_the_decision_notifies_the_adopter_with_the_matching_message(): void
    {
        $adopter = $this->loginAsTutor('adotante@example.com');
        $owner = $this->makeUser('dono@example.com');
        $adoption = $this->makeAdoption($adopter, $this->makeAdoptionPetFor($owner, ['name' => 'Bidu']));
        $this->switchTo($owner);

        $this->putJson("/api/v1/adoptions/{$adoption->uuid}/decision", ['status' => 'accepted'])->assertOk();
        $this->putJson("/api/v1/adoptions/{$adoption->uuid}/decision", ['status' => 'adopted'])->assertOk();

        $notifications = $adopter->notifications()->get()->sortBy('created_at')->values();
        $statuses = $notifications->pluck('data.status')->sort()->values()->all();
        $this->assertSame(['accepted', 'adopted'], $statuses);
        $this->assertSame('Interesse aceito!', $notifications->firstWhere('data.status', 'accepted')->data['title']);
        $this->assertSame('Adoção concluída!', $notifications->firstWhere('data.status', 'adopted')->data['title']);
        $this->assertSame($adoption->uuid, $notifications->first()->data['adoption_uuid']);
        $this->assertSame(0, $owner->notifications()->count());
    }

    public function test_a_declined_decision_notifies_and_an_invalid_one_does_not(): void
    {
        $adopter = $this->loginAsTutor('adotante@example.com');
        $owner = $this->makeUser('dono@example.com');
        $adoption = $this->makeAdoption($adopter, $this->makeAdoptionPetFor($owner));
        $this->switchTo($owner);

        $this->putJson("/api/v1/adoptions/{$adoption->uuid}/decision", ['status' => 'adopted'])->assertUnprocessable();
        $this->assertSame(0, $adopter->notifications()->count());

        $this->putJson("/api/v1/adoptions/{$adoption->uuid}/decision", ['status' => 'declined'])->assertOk();
        $this->assertSame('Interesse recusado', $adopter->notifications()->firstOrFail()->data['title']);
    }

    public function test_adoption_chat_notifies_the_adopter_when_the_owner_writes_and_the_managers_when_the_adopter_writes(): void
    {
        $adopter = $this->loginAsTutor('adotante@example.com');
        $creator = $this->makeUser('criador@example.com');
        $member = $this->makeUser('membro@example.com');
        $ong = $this->makeOng();
        $this->joinOng($ong, $creator, 'admin');
        $this->joinOng($ong, $member);
        $adoption = $this->makeAdoption($adopter, $this->makeAdoptionPetFor($creator, ['name' => 'Bidu', 'ong_id' => $ong->id]), 'accepted');
        $url = "/api/v1/adoptions/{$adoption->uuid}/messages";

        $this->postJson($url, ['body' => 'Olá!'])->assertCreated();
        $this->postJson($url, ['body' => 'Alguém aí?'])->assertCreated();

        foreach ([$creator, $member] as $manager) {
            $this->assertSame(1, $manager->notifications()->count());
            $this->assertSame('adoption', $manager->notifications()->firstOrFail()->data['kind']);
            $this->assertSame('Você recebeu uma mensagem sobre Bidu.', $manager->notifications()->firstOrFail()->data['body']);
        }
        $this->assertSame(0, $adopter->notifications()->count());

        $this->switchTo($member);
        $this->postJson($url, ['body' => 'Chegou!'])->assertCreated();

        $this->assertSame(1, $adopter->notifications()->count());
        $this->assertSame($adoption->uuid, $adopter->notifications()->firstOrFail()->data['adoption_uuid']);
        $this->assertSame(1, $creator->notifications()->count());
    }

    public function test_a_closed_chat_does_not_notify(): void
    {
        $adopter = $this->loginAsTutor('adotante@example.com');
        $owner = $this->makeUser('dono@example.com');
        $adoption = $this->makeAdoption($adopter, $this->makeAdoptionPetFor($owner), 'pending');

        $this->postJson("/api/v1/adoptions/{$adoption->uuid}/messages", ['body' => 'oi'])->assertUnprocessable();

        $this->assertSame(0, $owner->notifications()->count());
    }
}
