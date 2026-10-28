<?php

namespace Tests\Unit\Models;

use App\Models\PetWeight;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetWeightTest extends TestCase
{
    use MakesPet;
    use RefreshDatabase;

    protected function makePetWeight(array $attributes = []): PetWeight
    {
        return PetWeight::create(array_merge(['weight_kg' => 12.5, 'height_cm' => 40, 'width_cm' => 25, 'measured_at' => '2026-01-10', 'pet_id' => $this->makePet()->id], $attributes));
    }

    public function test_can_create(): void
    {
        $record = $this->makePetWeight();

        $this->assertDatabaseHas('pet_weights', ['id' => $record->id, 'weight_kg' => '12.50']);
    }

    public function test_can_read(): void
    {
        $record = $this->makePetWeight();

        $this->assertNotNull(PetWeight::find($record->id));
    }

    public function test_can_update(): void
    {
        $record = $this->makePetWeight();

        $record->update(['weight_kg' => '13.00']);

        $this->assertDatabaseHas('pet_weights', ['id' => $record->id, 'weight_kg' => '13.00']);
    }

    public function test_can_delete(): void
    {
        $record = $this->makePetWeight();

        $record->delete();

        $this->assertSoftDeleted('pet_weights', ['id' => $record->id]);
        $this->assertNull(PetWeight::find($record->id));
    }

    public function test_casts_measures(): void
    {
        $record = $this->makePetWeight();

        $this->assertSame('12.50', $record->weight_kg);
        $this->assertSame('40.00', $record->height_cm);
        $this->assertSame('25.00', $record->width_cm);
    }

    public function test_relationship(): void
    {
        $record = $this->makePetWeight();

        $this->assertInstanceOf(BelongsTo::class, $record->pet());
    }
}
