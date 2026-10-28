<?php

declare(strict_types=1);

namespace Tests\Feature\Adoption;

use App\Models\AdoptionSwipe;
use App\Models\Ong;
use App\Models\Species;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdoptionFeedTest extends TestCase
{
    use HasAdoptionFixtures, RefreshDatabase;

    private function names($response): array
    {
        return collect($response->json('data'))->pluck('name')->all();
    }

    public function test_requires_authentication(): void
    {
        $this->getJson('/api/v1/adoptions/feed')->assertUnauthorized();
    }

    public function test_lists_only_active_adoption_pets_of_other_users(): void
    {
        $user = $this->loginAsTutor();
        $owner = $this->makeUser('dono@example.com');
        $this->makeAdoptionPetFor($owner, ['name' => 'Bidu']);
        $this->makeAdoptionPetFor($owner, ['name' => 'Inativo', 'is_active' => false]);
        $this->makeAdoptionPetFor($user, ['name' => 'Meu']);
        $this->makePetFor($owner, ['name' => 'Pet de Match']);

        $this->assertSame(['Bidu'], $this->names($this->getJson('/api/v1/adoptions/feed')->assertOk()));
    }

    public function test_hides_pets_already_swiped(): void
    {
        $user = $this->loginAsTutor();
        $owner = $this->makeUser('dono@example.com');
        $seen = $this->makeAdoptionPetFor($owner, ['name' => 'Visto']);
        $this->makeAdoptionPetFor($owner, ['name' => 'Novo']);
        AdoptionSwipe::create(['action' => 'dislike', 'user_id' => $user->id, 'pet_id' => $seen->id]);

        $this->assertSame(['Novo'], $this->names($this->getJson('/api/v1/adoptions/feed')));
    }

    public function test_shows_ong_without_exposing_private_data(): void
    {
        $this->loginAsTutor();
        $ong = Ong::create(['name' => 'Patas', 'slug' => 'patas', 'city' => 'São Paulo', 'state' => 'SP', 'status' => 'verified']);
        $this->makeAdoptionPetFor($this->makeUser('dono@example.com'), ['ong_id' => $ong->id, 'latitude' => -23.5, 'longitude' => -46.6]);

        $item = $this->getJson('/api/v1/adoptions/feed')->assertOk()->json('data.0');

        $this->assertSame(['uuid' => $ong->uuid, 'name' => 'Patas', 'city' => 'São Paulo', 'state' => 'SP', 'is_verified' => true], $item['ong']);
        foreach (['id', 'user_id', 'latitude', 'longitude', 'ong_id'] as $key) {
            $this->assertArrayNotHasKey($key, $item);
        }
    }

    public function test_filters_by_species_gender_and_paginates(): void
    {
        $this->loginAsTutor();
        $owner = $this->makeUser('dono@example.com');
        $cat = Species::where('slug', 'cat')->firstOrFail();
        $this->makeAdoptionPetFor($owner, ['name' => 'Cachorro', 'gender' => 'male']);
        $this->makeAdoptionPetFor($owner, ['name' => 'Gata', 'species_id' => $cat->id, 'gender' => 'female']);
        $this->makeAdoptionPetFor($owner, ['name' => 'Cadela', 'gender' => 'female']);

        $this->assertSame(['Gata'], $this->names($this->getJson("/api/v1/adoptions/feed?filters[species_id]={$cat->id}")));
        $this->assertSame(['Cadela', 'Gata'], $this->names($this->getJson('/api/v1/adoptions/feed?filters[gender]=female')));
        $this->getJson('/api/v1/adoptions/feed?per_page=2')->assertJsonCount(2, 'data')->assertJsonPath('meta.total', 3);
    }

    public function test_distance_filter_uses_the_coordinates_sent_by_the_adopter(): void
    {
        $this->loginAsTutor();
        $owner = $this->makeUser('dono@example.com');
        $this->makeAdoptionPetFor($owner, ['name' => 'Perto', 'latitude' => -23.5605, 'longitude' => -46.6433]);
        $this->makeAdoptionPetFor($owner, ['name' => 'Rio', 'latitude' => -22.9068, 'longitude' => -43.1729]);
        $this->makeAdoptionPetFor($owner, ['name' => 'Sem Local']);
        $query = 'filters[latitude]=-23.5505&filters[longitude]=-46.6333';

        $near = $this->getJson("/api/v1/adoptions/feed?{$query}&filters[max_distance_km]=50")->assertOk();

        $this->assertSame(['Perto'], $this->names($near));
        $this->assertEqualsWithDelta(1.5, $near->json('data.0.distance_km'), 0.5);
        $this->assertEqualsWithDelta(357.0, collect($this->getJson("/api/v1/adoptions/feed?{$query}")->json('data'))->firstWhere('name', 'Rio')['distance_km'], 10.0);
    }

    public function test_distance_filter_requires_coordinates(): void
    {
        $this->loginAsTutor();

        $this->getJson('/api/v1/adoptions/feed?filters[max_distance_km]=10')->assertUnprocessable()->assertJsonPath('message', 'Informe a localização no seu perfil ou envie latitude e longitude.');
        $this->getJson('/api/v1/adoptions/feed?filters[latitude]=-23.5')->assertUnprocessable()->assertJsonValidationErrors('filters.longitude', 'errors');
        $this->getJson('/api/v1/adoptions/feed?filters[latitude]=999&filters[longitude]=0')->assertUnprocessable();
    }
}
