<?php

namespace Tests\Unit\Models;

use App\Models\Species;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpeciesTest extends TestCase
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

    public function test_can_create_species(): void
    {
        $species = $this->species();

        $this->assertDatabaseHas('species', [
            'id' => $species->id,
            'name' => 'Cachorro',
            'slug' => 'dog',
        ]);
    }

    public function test_can_read_species(): void
    {
        $species = $this->species();

        $found = Species::find($species->id);

        $this->assertNotNull($found);
        $this->assertSame('Cachorro', $found->name);
    }

    public function test_can_update_species(): void
    {
        $species = $this->species();

        $species->update(['name' => 'Gato', 'slug' => 'cat']);

        $this->assertDatabaseHas('species', [
            'id' => $species->id,
            'name' => 'Gato',
            'slug' => 'cat',
        ]);
    }

    public function test_can_delete_species(): void
    {
        $species = $this->species();

        $species->delete();

        $this->assertSoftDeleted('species', ['id' => $species->id]);
        $this->assertNull(Species::find($species->id));
    }

    public function test_species_slug_is_unique(): void
    {
        $this->species(['slug' => 'dog']);

        $this->expectException(QueryException::class);

        $this->species(['name' => 'Outro Cachorro', 'slug' => 'dog']);
    }

    public function test_species_casts_attributes(): void
    {
        $species = $this->species(['order' => '5', 'is_active' => 1]);

        $this->assertIsInt($species->order);
        $this->assertIsBool($species->is_active);
    }

    public function test_species_has_breeds_relationship(): void
    {
        $species = $this->species();

        $this->assertInstanceOf(
            HasMany::class,
            $species->breeds()
        );
    }

    public function test_species_has_pets_relationship(): void
    {
        $species = $this->species();

        $this->assertInstanceOf(
            HasMany::class,
            $species->pets()
        );
    }
}
