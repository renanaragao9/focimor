<?php

declare(strict_types=1);

namespace Tests\Feature\Global;

use App\Models\Breed;
use App\Models\Species;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SelectTest extends TestCase
{
    use RefreshDatabase;

    private function login(): void
    {
        $user = User::create(['name' => 'Tutor', 'email' => 'tutor@example.com', 'password' => 'Senha123']);
        $this->withToken($user->createToken('t')->plainTextToken);
    }

    public static function endpoints(): array
    {
        return [
            'espécies' => ['species'],
            'raças' => ['breeds'],
            'portes' => ['sizes'],
            'objetivos' => ['goals'],
            'tipos de registro' => ['health-record-types'],
        ];
    }

    #[DataProvider('endpoints')]
    public function test_select_is_public(string $endpoint): void
    {
        $this->getJson("/api/v1/selects/{$endpoint}")->assertOk();
    }

    #[DataProvider('endpoints')]
    public function test_select_lists_seeded_catalog(string $endpoint): void
    {
        $this->seed(LookupSeeder::class);
        $this->login();

        $this->getJson("/api/v1/selects/{$endpoint}")
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);
    }

    public function test_select_returns_only_active_items_in_order(): void
    {
        $this->seed(LookupSeeder::class);
        Species::where('slug', 'other')->update(['is_active' => false]);
        $this->login();

        $response = $this->getJson('/api/v1/selects/species')->assertOk();

        $this->assertSame(['dog', 'cat'], collect($response->json('data'))->pluck('slug')->all());
    }

    public function test_select_does_not_leak_extra_columns(): void
    {
        $this->seed(LookupSeeder::class);
        $this->login();

        $item = $this->getJson('/api/v1/selects/sizes')->json('data.0');

        $this->assertEqualsCanonicalizing(['id', 'name', 'slug'], array_keys($item));
    }

    public function test_breeds_can_be_filtered_by_species(): void
    {
        $this->seed(LookupSeeder::class);
        $this->login();
        $cat = Species::where('slug', 'cat')->firstOrFail();

        $response = $this->getJson("/api/v1/selects/breeds?species_id={$cat->id}")->assertOk();

        $breeds = collect($response->json('data'));
        $this->assertNotEmpty($breeds);
        $this->assertTrue($breeds->every(fn ($b) => $b['species_id'] === $cat->id));
        $this->assertLessThan(Breed::count(), $breeds->count());
    }

    public function test_breeds_without_filter_returns_at_most_twenty(): void
    {
        $this->seed(LookupSeeder::class);
        $this->login();

        $this->getJson('/api/v1/selects/breeds')->assertOk()->assertJsonCount(20, 'data');
    }

    public function test_breeds_can_be_searched_by_name(): void
    {
        $this->seed(LookupSeeder::class);
        $this->login();
        $dog = Species::where('slug', 'dog')->firstOrFail();

        $response = $this->getJson("/api/v1/selects/breeds?species_id={$dog->id}&search=retriever")->assertOk();

        $names = collect($response->json('data'))->pluck('name');
        $this->assertNotEmpty($names);
        $this->assertTrue($names->every(fn ($name) => str_contains(strtolower($name), 'retriever')));
        $this->assertContains('Golden Retriever', $names);
    }

    public function test_breeds_search_without_match_returns_empty(): void
    {
        $this->seed(LookupSeeder::class);
        $this->login();

        $this->getJson('/api/v1/selects/breeds?search=zzzxxyy')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_breeds_rejects_unknown_species(): void
    {
        $this->login();

        $this->getJson('/api/v1/selects/breeds?species_id=9999')
            ->assertUnprocessable()
            ->assertJsonPath('message', 'A espécie selecionada não existe.');
    }
}
