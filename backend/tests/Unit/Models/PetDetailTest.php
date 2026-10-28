<?php

namespace Tests\Unit\Models;

use App\Models\PetDetail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetDetailTest extends TestCase
{
    use MakesPet;
    use RefreshDatabase;

    protected function makePetDetail(array $attributes = []): PetDetail
    {
        return PetDetail::create(array_merge(['microchip' => '123456789', 'pet_id' => $this->makePet()->id], $attributes));
    }

    public function test_can_create(): void
    {
        $record = $this->makePetDetail();

        $this->assertDatabaseHas('pet_details', ['id' => $record->id, 'microchip' => '123456789']);
    }

    public function test_can_read(): void
    {
        $record = $this->makePetDetail();

        $this->assertNotNull(PetDetail::find($record->id));
    }

    public function test_can_update(): void
    {
        $record = $this->makePetDetail();

        $record->update(['microchip' => '987654321']);

        $this->assertDatabaseHas('pet_details', ['id' => $record->id, 'microchip' => '987654321']);
    }

    public function test_can_delete(): void
    {
        $record = $this->makePetDetail();

        $record->delete();

        $this->assertSoftDeleted('pet_details', ['id' => $record->id]);
        $this->assertNull(PetDetail::find($record->id));
    }

    public function test_relationship(): void
    {
        $record = $this->makePetDetail();

        $this->assertInstanceOf(BelongsTo::class, $record->pet());
    }
}
