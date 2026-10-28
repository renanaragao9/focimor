<?php

declare(strict_types=1);

namespace Tests\Feature\Pet;

use App\Models\Breed;
use App\Models\Pet;
use App\Models\Species;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PetTest extends TestCase
{
    use ActsAsTutor, RefreshDatabase;

    public function test_requires_authentication(): void
    {
        $this->getJson('/api/v1/pets')->assertUnauthorized();
        $this->postJson('/api/v1/pets', [])->assertUnauthorized();
    }

    public function test_store_creates_pet_for_the_authenticated_user(): void
    {
        $user = $this->loginAsTutor();

        $response = $this->postJson('/api/v1/pets', $this->petPayload())
            ->assertCreated()
            ->assertJsonPath('data.name', 'Thor')
            ->assertJsonPath('data.species.name', 'Cachorro')
            ->assertJsonPath('data.color', 'Caramelo')
            ->assertJsonPath('data.birth_date', '10/05/2022')
            ->assertJsonMissingPath('data.id')
            ->assertJsonMissingPath('data.user_id');

        $this->assertDatabaseHas('pets', ['uuid' => $response->json('data.uuid'), 'user_id' => $user->id]);
    }

    public function test_store_rejects_a_color_longer_than_fifty_characters(): void
    {
        $this->loginAsTutor();

        $this->postJson('/api/v1/pets', $this->petPayload(['color' => str_repeat('a', 51)]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('color');
    }

    public function test_store_ignores_user_id_and_ong_id_sent_by_the_client(): void
    {
        $user = $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');

        $this->postJson('/api/v1/pets', $this->petPayload(['user_id' => $other->id, 'ong_id' => 1]))->assertCreated();

        $pet = Pet::where('name', 'Thor')->firstOrFail();
        $this->assertSame($user->id, $pet->user_id);
        $this->assertNull($pet->ong_id);
    }

    public function test_store_accepts_breed_of_the_same_species_only(): void
    {
        $this->loginAsTutor();
        $dogBreed = Breed::whereHas('species', fn ($q) => $q->where('slug', 'dog'))->firstOrFail();
        $cat = Species::where('slug', 'cat')->firstOrFail();

        $this->postJson('/api/v1/pets', $this->petPayload(['breed_id' => $dogBreed->id]))->assertCreated();

        $this->postJson('/api/v1/pets', $this->petPayload(['breed_id' => $dogBreed->id, 'species_id' => $cat->id]))
            ->assertUnprocessable()
            ->assertJsonPath('message', 'A raça selecionada não existe para a espécie informada.');
    }

    #[DataProvider('invalidPayloads')]
    public function test_store_validates_input(array $overrides, string $field): void
    {
        $this->loginAsTutor();

        $this->postJson('/api/v1/pets', $this->petPayload($overrides))
            ->assertUnprocessable()
            ->assertJsonPath('status', 'error')
            ->assertJsonValidationErrors($field, 'errors');

        $this->assertSame(0, Pet::count());
    }

    public static function invalidPayloads(): array
    {
        return [
            'sem nome' => [['name' => ''], 'name'],
            'sexo inválido' => [['gender' => 'x'], 'gender'],
            'nascimento futuro' => [['birth_date' => '2999-01-01'], 'birth_date'],
            'latitude fora' => [['latitude' => 120], 'latitude'],
            'espécie inexistente' => [['species_id' => 9999], 'species_id'],
            'objetivo inexistente' => [['goal_id' => 9999], 'goal_id'],
        ];
    }

    public function test_index_lists_only_own_pets_with_pagination(): void
    {
        $user = $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $this->makePetFor($user, ['name' => 'Rex']);
        $this->makePetFor($user, ['name' => 'Luna']);
        $this->makePetFor($other, ['name' => 'Alheio']);

        $response = $this->getJson('/api/v1/pets')->assertOk();

        $this->assertEqualsCanonicalizing(['Rex', 'Luna'], collect($response->json('data'))->pluck('name')->all());
        $response->assertJsonPath('meta.total', 2);
    }

    public function test_index_search_filter_and_sort(): void
    {
        $user = $this->loginAsTutor();
        $cat = Species::where('slug', 'cat')->firstOrFail();
        $this->makePetFor($user, ['name' => 'Bidu']);
        $this->makePetFor($user, ['name' => 'Aslan', 'species_id' => $cat->id]);
        $this->makePetFor($user, ['name' => 'Zeca', 'is_active' => false]);

        $this->getJson('/api/v1/pets?search=bid')->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/pets?filters[species_id]={$cat->id}")->assertJsonCount(1, 'data')->assertJsonPath('data.0.name', 'Aslan');
        $this->getJson('/api/v1/pets?filters[is_active]=0')->assertJsonCount(1, 'data')->assertJsonPath('data.0.name', 'Zeca');

        $names = collect($this->getJson('/api/v1/pets?sort=name&direction=asc')->json('data'))->pluck('name')->all();
        $this->assertSame(['Aslan', 'Bidu', 'Zeca'], $names);
    }

    public function test_index_ignores_sort_by_columns_that_are_not_allowed(): void
    {
        $user = $this->loginAsTutor();
        $this->makePetFor($user);

        $this->getJson('/api/v1/pets?sort=password;drop')->assertOk();
        $this->getJson('/api/v1/pets?sort=user_id')->assertOk();
    }

    public function test_show_returns_pet_with_relations(): void
    {
        $user = $this->loginAsTutor();
        $pet = $this->makePetFor($user);

        $this->getJson("/api/v1/pets/{$pet->uuid}")
            ->assertOk()
            ->assertJsonPath('data.uuid', $pet->uuid)
            ->assertJsonStructure(['data' => ['species', 'goal', 'photos']]);
    }

    public function test_update_changes_only_sent_fields(): void
    {
        $user = $this->loginAsTutor();
        $pet = $this->makePetFor($user, ['bio' => 'original']);

        $this->putJson("/api/v1/pets/{$pet->uuid}", ['name' => 'Novo Nome'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Novo Nome')
            ->assertJsonPath('data.bio', 'original');
    }

    public function test_update_validates_breed_against_current_species(): void
    {
        $user = $this->loginAsTutor();
        $pet = $this->makePetFor($user);
        $catBreed = Breed::whereHas('species', fn ($q) => $q->where('slug', 'cat'))->firstOrFail();

        $this->putJson("/api/v1/pets/{$pet->uuid}", ['breed_id' => $catBreed->id])->assertUnprocessable();
    }

    public function test_destroy_soft_deletes_the_pet(): void
    {
        $user = $this->loginAsTutor();
        $pet = $this->makePetFor($user);

        $this->deleteJson("/api/v1/pets/{$pet->uuid}")->assertOk();

        $this->assertSoftDeleted('pets', ['id' => $pet->id]);
        $this->getJson("/api/v1/pets/{$pet->uuid}")->assertNotFound();
    }

    public function test_cannot_see_update_or_delete_pets_of_other_users(): void
    {
        $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $foreign = $this->makePetFor($other, ['name' => 'Alheio']);

        $this->getJson("/api/v1/pets/{$foreign->uuid}")->assertNotFound();
        $this->putJson("/api/v1/pets/{$foreign->uuid}", ['name' => 'Hackeado'])->assertNotFound();
        $this->deleteJson("/api/v1/pets/{$foreign->uuid}")->assertNotFound();

        $this->assertSame('Alheio', $foreign->fresh()->name);
        $this->assertNull($foreign->fresh()->deleted_at);
    }

    public function test_unknown_uuid_returns_not_found(): void
    {
        $this->loginAsTutor();

        $this->getJson('/api/v1/pets/00000000-0000-0000-0000-000000000000')->assertNotFound();
    }
}
