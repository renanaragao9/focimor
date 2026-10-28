<?php

namespace Tests\Unit\Models;

use App\Models\PetVaccine;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetVaccineTest extends TestCase
{
    use MakesPet;
    use RefreshDatabase;

    protected function makePetVaccine(array $attributes = []): PetVaccine
    {
        return PetVaccine::create(array_merge(['name' => 'V10', 'applied_at' => '2026-01-10', 'next_dose_at' => '2027-01-10', 'pet_id' => $this->makePet()->id], $attributes));
    }

    public function test_can_create(): void
    {
        $record = $this->makePetVaccine();

        $this->assertDatabaseHas('pet_vaccines', ['id' => $record->id, 'name' => 'V10']);
    }

    public function test_can_read(): void
    {
        $record = $this->makePetVaccine();

        $this->assertNotNull(PetVaccine::find($record->id));
    }

    public function test_can_update(): void
    {
        $record = $this->makePetVaccine();

        $record->update(['name' => 'Antirrábica']);

        $this->assertDatabaseHas('pet_vaccines', ['id' => $record->id, 'name' => 'Antirrábica']);
    }

    public function test_can_delete(): void
    {
        $record = $this->makePetVaccine();

        $record->delete();

        $this->assertSoftDeleted('pet_vaccines', ['id' => $record->id]);
        $this->assertNull(PetVaccine::find($record->id));
    }

    public function test_casts_dates(): void
    {
        $record = $this->makePetVaccine();

        $this->assertInstanceOf(Carbon::class, $record->applied_at);
        $this->assertInstanceOf(Carbon::class, $record->next_dose_at);
    }

    public function test_relationship(): void
    {
        $record = $this->makePetVaccine();

        $this->assertInstanceOf(BelongsTo::class, $record->pet());
    }
}
