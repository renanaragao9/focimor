<?php

declare(strict_types=1);

namespace Tests\Feature\PetMatch;

use App\Models\Message;
use App\Models\Pet;
use App\Models\PetMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Pet\ActsAsTutor;
use Tests\TestCase;

class PetMatchTest extends TestCase
{
    use ActsAsTutor, RefreshDatabase;

    private function makeMatch(Pet $a, Pet $b, array $overrides = []): PetMatch
    {
        [$one, $two] = $a->id < $b->id ? [$a, $b] : [$b, $a];

        return PetMatch::create(array_merge(['pet_one_id' => $one->id, 'pet_two_id' => $two->id, 'is_active' => true, 'matched_at' => now()], $overrides));
    }

    public function test_requires_authentication(): void
    {
        $this->getJson('/api/v1/matches')->assertUnauthorized();
        $this->deleteJson('/api/v1/matches/'.fake()->uuid())->assertUnauthorized();
        $this->getJson('/api/v1/matches/'.fake()->uuid())->assertUnauthorized();
    }

    public function test_shows_a_single_match_with_the_other_pet_and_unread_count(): void
    {
        $user = $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $rex = $this->makePetFor($user, ['name' => 'Rex']);
        $luna = $this->makePetFor($other, ['name' => 'Luna']);
        $match = $this->makeMatch($rex, $luna);
        Message::create(['body' => 'oi', 'pet_match_id' => $match->id, 'sender_user_id' => $other->id]);
        Message::create(['body' => 'tudo bem?', 'pet_match_id' => $match->id, 'sender_user_id' => $user->id]);

        $response = $this->getJson("/api/v1/matches/{$match->uuid}")->assertOk();

        $response->assertJsonPath('data.uuid', $match->uuid)
            ->assertJsonPath('data.my_pet.name', 'Rex')
            ->assertJsonPath('data.other_pet.name', 'Luna')
            ->assertJsonPath('data.unread_count', 1)
            ->assertJsonStructure(['data' => ['other_pet' => ['uuid', 'species', 'photos']]]);
        foreach (['id', 'user_id', 'latitude', 'longitude'] as $key) {
            $this->assertArrayNotHasKey($key, $response->json('data.other_pet'));
        }
    }

    public function test_cannot_show_matches_that_are_not_mine_or_were_undone(): void
    {
        $user = $this->loginAsTutor();
        $foreign = $this->makeMatch($this->makePetFor($this->makeUser('a@example.com')), $this->makePetFor($this->makeUser('b@example.com')));
        $undone = $this->makeMatch($this->makePetFor($user, ['name' => 'Rex']), $this->makePetFor($this->makeUser('c@example.com')), ['is_active' => false]);

        $this->getJson("/api/v1/matches/{$foreign->uuid}")->assertNotFound();
        $this->getJson("/api/v1/matches/{$undone->uuid}")->assertNotFound();
        $this->getJson('/api/v1/matches/'.fake()->uuid())->assertNotFound();
    }

    public function test_lists_my_matches_from_either_side_with_the_other_pet(): void
    {
        $user = $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $rex = $this->makePetFor($user, ['name' => 'Rex']);
        $luna = $this->makePetFor($other, ['name' => 'Luna']);
        $thor = $this->makePetFor($user, ['name' => 'Thor']);
        $mimi = $this->makePetFor($other, ['name' => 'Mimi']);
        $this->makeMatch($rex, $luna);
        $this->makeMatch($mimi, $thor);

        $response = $this->getJson('/api/v1/matches')->assertOk();

        $pairs = collect($response->json('data'))->map(fn ($m) => $m['my_pet']['name'].'>'.$m['other_pet']['name'])->all();
        $this->assertEqualsCanonicalizing(['Rex>Luna', 'Thor>Mimi'], $pairs);
        $response->assertJsonPath('meta.total', 2);
    }

    public function test_does_not_list_matches_of_other_users_inactive_or_with_deleted_pets(): void
    {
        $user = $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $third = $this->makeUser('terceiro@example.com');
        $rex = $this->makePetFor($user, ['name' => 'Rex']);
        $luna = $this->makePetFor($other, ['name' => 'Luna']);
        $zeca = $this->makePetFor($other, ['name' => 'Zeca']);
        $bia = $this->makePetFor($third, ['name' => 'Bia']);
        $this->makeMatch($luna, $bia);
        $this->makeMatch($rex, $luna, ['is_active' => false]);
        $this->makeMatch($rex, $zeca);
        $zeca->delete();

        $this->getJson('/api/v1/matches')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_filters_by_my_pet(): void
    {
        $user = $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $rex = $this->makePetFor($user, ['name' => 'Rex']);
        $thor = $this->makePetFor($user, ['name' => 'Thor']);
        $luna = $this->makePetFor($other, ['name' => 'Luna']);
        $mimi = $this->makePetFor($other, ['name' => 'Mimi']);
        $this->makeMatch($rex, $luna);
        $this->makeMatch($thor, $mimi);

        $this->getJson("/api/v1/matches?filters[pet_uuid]={$rex->uuid}")->assertJsonCount(1, 'data')->assertJsonPath('data.0.my_pet.name', 'Rex');
        $this->getJson("/api/v1/matches?filters[pet_uuid]={$luna->uuid}")->assertJsonCount(0, 'data');
        $this->getJson('/api/v1/matches?filters[pet_uuid]=x')->assertUnprocessable();
    }

    public function test_match_listing_does_not_expose_private_data(): void
    {
        $user = $this->loginAsTutor();
        $this->makeMatch($this->makePetFor($user), $this->makePetFor($this->makeUser('outro@example.com'), ['latitude' => -23.5, 'longitude' => -46.6]));

        $item = $this->getJson('/api/v1/matches')->json('data.0.other_pet');

        foreach (['id', 'user_id', 'latitude', 'longitude'] as $key) {
            $this->assertArrayNotHasKey($key, $item);
        }
    }

    public function test_unmatch_hides_the_match_and_keeps_the_record(): void
    {
        $user = $this->loginAsTutor();
        $match = $this->makeMatch($this->makePetFor($user), $this->makePetFor($this->makeUser('outro@example.com')));

        $this->deleteJson("/api/v1/matches/{$match->uuid}")->assertOk();

        $this->assertFalse($match->fresh()->is_active);
        $this->getJson('/api/v1/matches')->assertJsonCount(0, 'data');
        $this->deleteJson("/api/v1/matches/{$match->uuid}")->assertNotFound();
    }

    public function test_cannot_unmatch_a_match_that_is_not_mine(): void
    {
        $this->loginAsTutor();
        $match = $this->makeMatch(
            $this->makePetFor($this->makeUser('a@example.com')),
            $this->makePetFor($this->makeUser('b@example.com')),
        );

        $this->deleteJson("/api/v1/matches/{$match->uuid}")->assertNotFound();

        $this->assertTrue($match->fresh()->is_active);
    }

    public function test_full_flow_feed_swipe_match_list(): void
    {
        $alice = $this->loginAsTutor('alice@example.com');
        $bob = $this->makeUser('bob@example.com');
        $rex = $this->makePetFor($alice, ['name' => 'Rex']);
        $luna = $this->makePetFor($bob, ['name' => 'Luna']);

        $this->getJson("/api/v1/pets/{$rex->uuid}/feed")->assertJsonPath('data.0.uuid', $luna->uuid);
        $this->postJson("/api/v1/pets/{$rex->uuid}/swipes", ['target_pet_uuid' => $luna->uuid, 'action' => 'like'])->assertJsonPath('data.matched', false);
        $this->getJson("/api/v1/pets/{$rex->uuid}/feed")->assertJsonCount(0, 'data');

        $this->switchTo($bob);
        $this->getJson("/api/v1/pets/{$luna->uuid}/feed")->assertJsonPath('data.0.uuid', $rex->uuid);
        $this->postJson("/api/v1/pets/{$luna->uuid}/swipes", ['target_pet_uuid' => $rex->uuid, 'action' => 'like'])->assertJsonPath('data.matched', true);
        $this->getJson('/api/v1/matches')->assertJsonCount(1, 'data')->assertJsonPath('data.0.other_pet.name', 'Rex');

        $this->switchTo($alice);
        $this->getJson('/api/v1/matches')->assertJsonCount(1, 'data')->assertJsonPath('data.0.other_pet.name', 'Luna');
    }
}
