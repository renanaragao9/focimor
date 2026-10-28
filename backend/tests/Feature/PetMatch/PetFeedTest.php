<?php

declare(strict_types=1);

namespace Tests\Feature\PetMatch;

use App\Models\Goal;
use App\Models\Species;
use App\Models\Swipe;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Pet\ActsAsTutor;
use Tests\TestCase;

class PetFeedTest extends TestCase
{
    use ActsAsTutor, RefreshDatabase;

    private const SAO_PAULO = ['latitude' => -23.5505, 'longitude' => -46.6333];

    private function names($response): array
    {
        return collect($response->json('data'))->pluck('name')->all();
    }

    public function test_requires_authentication(): void
    {
        $this->seed(LookupSeeder::class);
        $pet = $this->makePetFor($this->makeUser('a@example.com'));

        $this->getJson("/api/v1/pets/{$pet->uuid}/feed")->assertUnauthorized();
    }

    public function test_cannot_open_the_feed_of_a_pet_that_is_not_mine(): void
    {
        $this->loginAsTutor();
        $foreign = $this->makePetFor($this->makeUser('outro@example.com'));

        $this->getJson("/api/v1/pets/{$foreign->uuid}/feed")->assertNotFound();
    }

    public function test_lists_pets_of_other_users_and_hides_my_own(): void
    {
        $user = $this->loginAsTutor();
        $mine = $this->makePetFor($user, ['name' => 'Rex']);
        $this->makePetFor($user, ['name' => 'Meu Outro Pet']);
        $this->makePetFor($this->makeUser('outro@example.com'), ['name' => 'Luna']);

        $response = $this->getJson("/api/v1/pets/{$mine->uuid}/feed")->assertOk();

        $this->assertSame(['Luna'], $this->names($response));
    }

    public function test_feed_does_not_expose_private_data_of_other_pets(): void
    {
        $user = $this->loginAsTutor();
        $mine = $this->makePetFor($user);
        $this->makePetFor($this->makeUser('outro@example.com'), ['name' => 'Luna', ...self::SAO_PAULO]);

        $item = $this->getJson("/api/v1/pets/{$mine->uuid}/feed")->json('data.0');

        foreach (['id', 'user_id', 'latitude', 'longitude', 'ong_id'] as $key) {
            $this->assertArrayNotHasKey($key, $item);
        }
        $this->assertNotEmpty($item['uuid']);
    }

    public function test_hides_pets_already_swiped_inactive_and_adoption_pets(): void
    {
        $user = $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $mine = $this->makePetFor($user);
        $seen = $this->makePetFor($other, ['name' => 'Ja Visto']);
        $this->makePetFor($other, ['name' => 'Inativo', 'is_active' => false]);
        $this->makePetFor($other, ['name' => 'Adocao', 'goal_id' => Goal::where('slug', 'adoption')->value('id')]);
        $this->makePetFor($other, ['name' => 'Novo']);
        Swipe::create(['action' => 'dislike', 'swiper_pet_id' => $mine->id, 'target_pet_id' => $seen->id]);

        $this->assertSame(['Novo'], $this->names($this->getJson("/api/v1/pets/{$mine->uuid}/feed")));
    }

    public function test_defaults_to_same_species_and_goal_and_accepts_overrides(): void
    {
        $user = $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $mine = $this->makePetFor($user);
        $cat = Species::where('slug', 'cat')->firstOrFail();
        $breeding = Goal::where('slug', 'breeding')->firstOrFail();
        $this->makePetFor($other, ['name' => 'Cachorro Amigo']);
        $this->makePetFor($other, ['name' => 'Gato', 'species_id' => $cat->id]);
        $this->makePetFor($other, ['name' => 'Cachorro Cruza', 'goal_id' => $breeding->id]);

        $this->assertSame(['Cachorro Amigo'], $this->names($this->getJson("/api/v1/pets/{$mine->uuid}/feed")));
        $this->assertSame(['Gato'], $this->names($this->getJson("/api/v1/pets/{$mine->uuid}/feed?filters[species_id]={$cat->id}")));
        $this->assertSame(['Cachorro Cruza'], $this->names($this->getJson("/api/v1/pets/{$mine->uuid}/feed?filters[goal_id]={$breeding->id}")));
    }

    public function test_filters_by_gender(): void
    {
        $user = $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $mine = $this->makePetFor($user);
        $this->makePetFor($other, ['name' => 'Macho', 'gender' => 'male']);
        $this->makePetFor($other, ['name' => 'Femea', 'gender' => 'female']);

        $this->assertSame(['Femea'], $this->names($this->getJson("/api/v1/pets/{$mine->uuid}/feed?filters[gender]=female")));
    }

    public function test_paginates_newest_first(): void
    {
        $user = $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $mine = $this->makePetFor($user);
        foreach (['A', 'B', 'C'] as $name) {
            $this->makePetFor($other, ['name' => $name]);
        }

        $response = $this->getJson("/api/v1/pets/{$mine->uuid}/feed?per_page=2")->assertOk();

        $this->assertSame(['C', 'B'], $this->names($response));
        $response->assertJsonPath('meta.total', 3);
    }

    public function test_max_distance_keeps_only_nearby_pets_sorted_with_distance(): void
    {
        $user = $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $mine = $this->makePetFor($user, self::SAO_PAULO);
        $this->makePetFor($other, ['name' => 'Perto', 'latitude' => -23.5605, 'longitude' => -46.6433]);
        $this->makePetFor($other, ['name' => 'Medio', 'latitude' => -23.6505, 'longitude' => -46.7333]);
        $this->makePetFor($other, ['name' => 'Rio', 'latitude' => -22.9068, 'longitude' => -43.1729]);
        $this->makePetFor($other, ['name' => 'Sem Local']);

        $response = $this->getJson("/api/v1/pets/{$mine->uuid}/feed?filters[max_distance_km]=50")->assertOk();

        $this->assertSame(['Perto', 'Medio'], $this->names($response));
        $this->assertEqualsWithDelta(1.5, $response->json('data.0.distance_km'), 0.5);
        $this->assertGreaterThan($response->json('data.0.distance_km'), $response->json('data.1.distance_km'));
        $response->assertJsonPath('meta.total', 2);
    }

    public function test_distance_is_shown_without_filter_when_both_have_location(): void
    {
        $user = $this->loginAsTutor();
        $mine = $this->makePetFor($user, self::SAO_PAULO);
        $this->makePetFor($this->makeUser('outro@example.com'), ['name' => 'Rio', 'latitude' => -22.9068, 'longitude' => -43.1729]);

        $distance = $this->getJson("/api/v1/pets/{$mine->uuid}/feed")->json('data.0.distance_km');

        $this->assertEqualsWithDelta(357.0, $distance, 10.0);
    }

    public function test_distance_filter_requires_the_pet_location(): void
    {
        $mine = $this->makePetFor($this->loginAsTutor());

        $this->getJson("/api/v1/pets/{$mine->uuid}/feed?filters[max_distance_km]=10")
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Informe a localização do seu pet para filtrar por distância.');
    }

    public function test_validates_filters(): void
    {
        $mine = $this->makePetFor($this->loginAsTutor());

        $this->getJson("/api/v1/pets/{$mine->uuid}/feed?filters[gender]=x")->assertUnprocessable();
        $this->getJson("/api/v1/pets/{$mine->uuid}/feed?filters[max_distance_km]=9999")->assertUnprocessable();
        $this->getJson("/api/v1/pets/{$mine->uuid}/feed?filters[species_id]=9999")->assertUnprocessable();
    }
}
