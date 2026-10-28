<?php

namespace Tests\Unit\Models;

use App\Models\Breed;
use App\Models\Species;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreedTest extends TestCase
{
    use RefreshDatabase;

    protected function species(array $attributes = []): Species
    {
        return Species::create(array_merge([
            'name' => 'Cachorro',
            'slug' => 'dog',
            'order' => 1,
            'is_active' => true,
        ], $attributes));
    }

    protected function makeBreed(array $attributes = []): Breed
    {
        $attributes['species_id'] ??= $this->species()->id;

        return Breed::create(array_merge([
            'name' => 'Labrador',
            'slug' => 'labrador',
            'order' => 1,
            'is_active' => true,
        ], $attributes));
    }

    public function test_can_create_breed(): void
    {
        $breed = $this->makeBreed();

        $this->assertDatabaseHas('breeds', [
            'id' => $breed->id,
            'name' => 'Labrador',
            'slug' => 'labrador',
        ]);
    }

    public function test_can_read_breed(): void
    {
        $breed = $this->makeBreed();

        $found = Breed::find($breed->id);

        $this->assertNotNull($found);
        $this->assertSame('Labrador', $found->name);
    }

    public function test_can_update_breed(): void
    {
        $breed = $this->makeBreed();

        $breed->update(['name' => 'Poodle', 'slug' => 'poodle']);

        $this->assertDatabaseHas('breeds', [
            'id' => $breed->id,
            'name' => 'Poodle',
            'slug' => 'poodle',
        ]);
    }

    public function test_can_delete_breed(): void
    {
        $breed = $this->makeBreed();

        $breed->delete();

        $this->assertSoftDeleted('breeds', ['id' => $breed->id]);
        $this->assertNull(Breed::find($breed->id));
    }

    public function test_breed_casts_attributes(): void
    {
        $breed = $this->makeBreed(['order' => '2', 'is_active' => 1]);

        $this->assertIsInt($breed->order);
        $this->assertIsBool($breed->is_active);
    }

    public function test_breed_slug_is_unique_per_species(): void
    {
        $speciesId = $this->species()->id;

        $this->makeBreed(['slug' => 'labrador', 'species_id' => $speciesId]);

        $this->expectException(QueryException::class);

        $this->makeBreed(['name' => 'Outro', 'slug' => 'labrador', 'species_id' => $speciesId]);
    }

    public function test_breed_slug_can_repeat_across_species(): void
    {
        $dog = $this->species(['name' => 'Cachorro', 'slug' => 'dog']);
        $cat = $this->species(['name' => 'Gato', 'slug' => 'cat']);

        $this->makeBreed(['slug' => 'srd', 'species_id' => $dog->id]);
        $second = $this->makeBreed(['slug' => 'srd', 'species_id' => $cat->id]);

        $this->assertDatabaseHas('breeds', [
            'id' => $second->id,
            'slug' => 'srd',
            'species_id' => $cat->id,
        ]);
    }

    public function test_breed_belongs_to_species(): void
    {
        $breed = $this->makeBreed();

        $this->assertInstanceOf(BelongsTo::class, $breed->species());
        $this->assertInstanceOf(Species::class, $breed->species()->first());
    }

    public function test_breed_has_pets_relationship(): void
    {
        $breed = $this->makeBreed();

        $this->assertInstanceOf(HasMany::class, $breed->pets());
    }
}
