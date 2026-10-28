<?php

declare(strict_types=1);

namespace Tests\Feature\Adoption;

use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Ong\HasOngFixtures;
use Tests\TestCase;

class AdoptablePetTest extends TestCase
{
    use HasOngFixtures, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LookupSeeder::class);
    }

    private function names($response): array
    {
        return collect($response->json('data'))->pluck('name')->all();
    }

    public function test_lists_only_active_adoption_pets_of_verified_ongs_without_authentication(): void
    {
        $owner = $this->makeUser('dono@example.com');
        $ong = $this->makeOng();
        $pending = $this->makeOng(['status' => 'pending']);
        $this->makeAdoptionPetFor($owner, ['name' => 'Bidu', 'ong_id' => $ong->id]);
        $this->makeAdoptionPetFor($owner, ['name' => 'Inativo', 'ong_id' => $ong->id, 'is_active' => false]);
        $this->makeAdoptionPetFor($owner, ['name' => 'Pendente', 'ong_id' => $pending->id]);
        $this->makeAdoptionPetFor($owner, ['name' => 'Particular']);
        $this->makePetFor($owner, ['name' => 'Amizade', 'ong_id' => $ong->id]);

        $response = $this->getJson('/api/v1/adoptable-pets')->assertOk();

        $this->assertSame(['Bidu'], $this->names($response));
        $item = $response->json('data.0');
        foreach (['id', 'latitude', 'longitude', 'user_id'] as $key) {
            $this->assertArrayNotHasKey($key, $item);
        }
        $this->assertSame($ong->name, $item['ong']['name']);
    }

    public function test_filters_and_search(): void
    {
        $owner = $this->makeUser('dono@example.com');
        $ong = $this->makeOng();
        $this->makeAdoptionPetFor($owner, ['name' => 'Bidu', 'ong_id' => $ong->id, 'gender' => 'male']);
        $this->makeAdoptionPetFor($owner, ['name' => 'Nina', 'ong_id' => $ong->id, 'gender' => 'female']);

        $this->assertSame(['Nina'], $this->names($this->getJson('/api/v1/adoptable-pets?filters[gender]=female')->assertOk()));
        $this->assertSame(['Bidu'], $this->names($this->getJson('/api/v1/adoptable-pets?search=bid')->assertOk()));
        $this->getJson('/api/v1/adoptable-pets?filters[gender]=x')->assertUnprocessable();
    }

    public function test_shows_one_adoptable_pet_and_hides_the_others(): void
    {
        $owner = $this->makeUser('dono@example.com');
        $ong = $this->makeOng();
        $pet = $this->makeAdoptionPetFor($owner, ['name' => 'Bidu', 'ong_id' => $ong->id]);
        $private = $this->makeAdoptionPetFor($owner, ['name' => 'Particular']);

        $this->getJson("/api/v1/adoptable-pets/{$pet->uuid}")->assertOk()->assertJsonPath('data.name', 'Bidu')->assertJsonPath('data.ong.uuid', $ong->uuid);
        $this->getJson("/api/v1/adoptable-pets/{$private->uuid}")->assertNotFound();
    }
}
