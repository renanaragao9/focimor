<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\Pet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Ong\HasOngFixtures;
use Tests\TestCase;

class ProfileLocationTest extends TestCase
{
    use HasOngFixtures, RefreshDatabase;

    private function names($response): array
    {
        return collect($response->json('data'))->pluck('name')->all();
    }

    private function seedAdoptionPets(): void
    {
        $owner = $this->makeUser('dono@example.com');
        $this->makeAdoptionPetFor($owner, ['name' => 'Perto', 'latitude' => -23.5605, 'longitude' => -46.6433]);
        $this->makeAdoptionPetFor($owner, ['name' => 'Rio', 'latitude' => -22.9068, 'longitude' => -43.1729]);
    }

    public function test_adoption_feed_uses_the_saved_location_by_default(): void
    {
        $user = $this->loginAsTutor();
        $user->update(['latitude' => -23.5505, 'longitude' => -46.6333]);
        $this->seedAdoptionPets();

        $near = $this->getJson('/api/v1/adoptions/feed?filters[max_distance_km]=50')->assertOk();

        $this->assertSame(['Perto'], $this->names($near));
        $this->assertEqualsWithDelta(1.5, $near->json('data.0.distance_km'), 0.5);
        $this->assertNotNull(collect($this->getJson('/api/v1/adoptions/feed')->json('data'))->firstWhere('name', 'Rio')['distance_km']);
    }

    public function test_coordinates_sent_in_the_request_override_the_saved_location(): void
    {
        $user = $this->loginAsTutor();
        $user->update(['latitude' => -23.5505, 'longitude' => -46.6333]);
        $this->seedAdoptionPets();

        $rio = $this->getJson('/api/v1/adoptions/feed?filters[latitude]=-22.9&filters[longitude]=-43.17&filters[max_distance_km]=50')->assertOk();

        $this->assertSame(['Rio'], $this->names($rio));
    }

    public function test_distance_filter_without_any_location_is_rejected(): void
    {
        $this->loginAsTutor();
        $this->seedAdoptionPets();

        $this->getJson('/api/v1/adoptions/feed?filters[max_distance_km]=50')
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Informe a localização no seu perfil ou envie latitude e longitude.');
        $this->getJson('/api/v1/adoptions/feed')->assertOk()->assertJsonCount(2, 'data')->assertJsonPath('data.0.distance_km', null);
    }

    public function test_pet_created_without_location_inherits_the_owners_saved_location(): void
    {
        $user = $this->loginAsTutor();
        $user->update(['latitude' => -23.5505, 'longitude' => -46.6333]);

        $uuid = $this->postJson('/api/v1/pets', $this->petPayload(['latitude' => null, 'longitude' => null]))->assertCreated()->json('data.uuid');

        $pet = Pet::where('uuid', $uuid)->firstOrFail();
        $this->assertEquals(-23.5505, $pet->latitude);
        $this->assertEquals(-46.6333, $pet->longitude);
    }

    public function test_pet_location_sent_by_the_client_wins(): void
    {
        $user = $this->loginAsTutor();
        $user->update(['latitude' => -23.5505, 'longitude' => -46.6333]);

        $uuid = $this->postJson('/api/v1/pets', $this->petPayload(['latitude' => -22.9, 'longitude' => -43.17]))->assertCreated()->json('data.uuid');

        $this->assertEquals(-22.9, Pet::where('uuid', $uuid)->firstOrFail()->latitude);
    }

    public function test_pet_without_any_location_stays_without_it(): void
    {
        $this->loginAsTutor();

        $uuid = $this->postJson('/api/v1/pets', $this->petPayload(['latitude' => null, 'longitude' => null]))->assertCreated()->json('data.uuid');

        $this->assertNull(Pet::where('uuid', $uuid)->firstOrFail()->latitude);
    }

    public function test_user_location_is_never_exposed_to_other_users(): void
    {
        $user = $this->loginAsTutor();
        $user->update(['latitude' => -23.5505, 'longitude' => -46.6333]);
        $owner = $this->makeUser('dono@example.com');
        $owner->update(['latitude' => -10.1, 'longitude' => -20.2]);
        $ong = $this->makeOng(['name' => 'Patas']);
        $this->joinOng($ong, $owner, 'admin');
        $this->joinOng($ong, $user);

        $members = $this->getJson("/api/v1/my-ongs/{$ong->uuid}/members")->assertOk()->json('data');

        foreach ($members as $member) {
            $this->assertArrayNotHasKey('latitude', $member);
            $this->assertArrayNotHasKey('avatar_path', $member);
        }
    }
}
