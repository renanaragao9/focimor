<?php

namespace Tests\Unit\Models;

use App\Models\Breed;
use App\Models\Goal;
use App\Models\Ong;
use App\Models\Pet;
use App\Models\PetPhoto;
use App\Models\Size;
use App\Models\Species;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PetTest extends TestCase
{
    use RefreshDatabase;

    protected function makePet(array $attributes = []): Pet
    {
        $user = User::create([
            'name' => 'Tutor',
            'email' => 'tutor'.Str::random(6).'@example.com',
            'password' => 'password',
        ]);

        $species = Species::create([
            'name' => 'Cachorro', 'slug' => 'dog-'.Str::random(5), 'order' => 1, 'is_active' => true,
        ]);

        $goal = Goal::create([
            'name' => 'Amizade', 'slug' => 'friendship-'.Str::random(5), 'order' => 1, 'is_active' => true,
        ]);

        return Pet::create(array_merge([
            'name' => 'Rex',
            'gender' => 'male',
            'bio' => 'Dócil',
            'is_neutered' => true,
            'is_vaccinated' => true,
            'is_active' => true,
            'user_id' => $user->id,
            'species_id' => $species->id,
            'goal_id' => $goal->id,
        ], $attributes));
    }

    public function test_managed_by_scope_includes_own_pets_and_pets_of_my_ongs(): void
    {
        $mine = $this->makePet(['name' => 'Meu']);
        $ong = Ong::create(['name' => 'ONG', 'slug' => 'ong', 'status' => 'verified']);
        $stranger = User::create(['name' => 'Outro', 'email' => 'outro@example.com', 'password' => 'password']);
        $ongPet = $this->makePet(['name' => 'Da ONG', 'ong_id' => $ong->id, 'user_id' => $stranger->id]);
        $this->makePet(['name' => 'Alheio', 'user_id' => $stranger->id]);
        $ong->users()->attach($mine->user_id, ['role' => 'member']);

        $names = Pet::managedBy($mine->user_id)->orderBy('name')->pluck('name')->all();

        $this->assertSame(['Da ONG', 'Meu'], $names);
        $this->assertTrue(Pet::managedBy($mine->user_id)->whereKey($ongPet->id)->exists());
        $this->assertFalse(Pet::managedBy($stranger->id)->where('name', 'Meu')->exists());
    }

    public function test_managers_returns_the_owner_and_the_members_of_the_ong(): void
    {
        $pet = $this->makePet(['name' => 'Bidu']);
        $ong = Ong::create(['name' => 'ONG', 'slug' => 'ong', 'status' => 'verified']);
        $member = User::create(['name' => 'Membro', 'email' => 'membro@example.com', 'password' => 'password']);
        $outsider = User::create(['name' => 'Fora', 'email' => 'fora@example.com', 'password' => 'password']);

        $this->assertSame([$pet->user_id], $pet->managers()->pluck('id')->all());

        $ong->users()->attach($member->id, ['role' => 'member']);
        $pet->update(['ong_id' => $ong->id]);

        $this->assertEqualsCanonicalizing([$pet->user_id, $member->id], $pet->managers()->pluck('id')->all());
        $this->assertNotContains($outsider->id, $pet->managers()->pluck('id')->all());
    }

    public function test_can_create_pet(): void
    {
        $pet = $this->makePet();

        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
            'name' => 'Rex',
            'gender' => 'male',
        ]);
    }

    public function test_pet_gets_uuid_on_creation(): void
    {
        $pet = $this->makePet();

        $this->assertNotEmpty($pet->uuid);
        $this->assertTrue(Str::isUuid($pet->uuid));
    }

    public function test_pet_route_key_is_uuid(): void
    {
        $pet = $this->makePet();

        $this->assertSame('uuid', $pet->getRouteKeyName());
    }

    public function test_can_update_pet(): void
    {
        $pet = $this->makePet();

        $pet->update(['name' => 'Bob', 'gender' => 'female']);

        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
            'name' => 'Bob',
            'gender' => 'female',
        ]);
    }

    public function test_can_delete_pet(): void
    {
        $pet = $this->makePet();

        $pet->delete();

        $this->assertSoftDeleted('pets', ['id' => $pet->id]);
        $this->assertNull(Pet::find($pet->id));
    }

    public function test_pet_casts_attributes(): void
    {
        $pet = $this->makePet(['is_active' => 1, 'is_neutered' => 0]);

        $this->assertIsBool($pet->is_active);
        $this->assertIsBool($pet->is_neutered);
        $this->assertIsBool($pet->is_vaccinated);
    }

    public function test_pet_belongs_to_relationships(): void
    {
        $pet = $this->makePet();

        $this->assertInstanceOf(BelongsTo::class, $pet->user());
        $this->assertInstanceOf(BelongsTo::class, $pet->species());
        $this->assertInstanceOf(BelongsTo::class, $pet->breed());
        $this->assertInstanceOf(BelongsTo::class, $pet->size());
        $this->assertInstanceOf(BelongsTo::class, $pet->goal());
    }

    public function test_pet_has_many_photos(): void
    {
        $pet = $this->makePet();

        PetPhoto::create([
            'path' => 'pet-photos/foto.jpg',
            'order' => 1,
            'is_primary' => true,
            'pet_id' => $pet->id,
        ]);

        $this->assertInstanceOf(HasMany::class, $pet->photos());
        $this->assertCount(1, $pet->photos()->get());
    }

    public function test_pet_optional_foreign_keys_can_be_null(): void
    {
        $pet = $this->makePet();

        $this->assertNull($pet->breed_id);
        $this->assertNull($pet->size_id);
    }

    public function test_pet_can_have_breed_and_size(): void
    {
        $species = Species::create([
            'name' => 'Gato', 'slug' => 'cat-'.Str::random(5), 'order' => 2, 'is_active' => true,
        ]);
        $breed = Breed::create([
            'name' => 'Siamês', 'slug' => 'siames-'.Str::random(5), 'order' => 1, 'is_active' => true, 'species_id' => $species->id,
        ]);
        $size = Size::create([
            'name' => 'Pequeno', 'slug' => 'small-'.Str::random(5), 'order' => 1, 'is_active' => true,
        ]);

        $pet = $this->makePet([
            'species_id' => $species->id,
            'breed_id' => $breed->id,
            'size_id' => $size->id,
        ]);

        $this->assertSame($breed->id, $pet->breed_id);
        $this->assertSame($size->id, $pet->size_id);
    }
}
