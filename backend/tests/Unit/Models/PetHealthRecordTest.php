<?php

namespace Tests\Unit\Models;

use App\Models\HealthRecordType;
use App\Models\PetHealthRecord;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PetHealthRecordTest extends TestCase
{
    use MakesPet;
    use RefreshDatabase;

    protected function makePetHealthRecord(array $attributes = []): PetHealthRecord
    {
        return PetHealthRecord::create(array_merge(['title' => 'Consulta anual', 'performed_at' => '2026-01-10', 'cost' => 150, 'health_record_type_id' => HealthRecordType::create(['name' => 'Consulta', 'slug' => 'consultation-'.Str::random(5), 'order' => 1, 'is_active' => true])->id, 'pet_id' => $this->makePet()->id], $attributes));
    }

    public function test_can_create(): void
    {
        $record = $this->makePetHealthRecord();

        $this->assertDatabaseHas('pet_health_records', ['id' => $record->id, 'title' => 'Consulta anual']);
    }

    public function test_can_read(): void
    {
        $record = $this->makePetHealthRecord();

        $this->assertNotNull(PetHealthRecord::find($record->id));
    }

    public function test_can_update(): void
    {
        $record = $this->makePetHealthRecord();

        $record->update(['title' => 'Retorno']);

        $this->assertDatabaseHas('pet_health_records', ['id' => $record->id, 'title' => 'Retorno']);
    }

    public function test_can_delete(): void
    {
        $record = $this->makePetHealthRecord();

        $record->delete();

        $this->assertSoftDeleted('pet_health_records', ['id' => $record->id]);
        $this->assertNull(PetHealthRecord::find($record->id));
    }

    public function test_belongs_to_pet(): void
    {
        $this->assertInstanceOf(BelongsTo::class, $this->makePetHealthRecord()->pet());
    }

    public function test_casts_cost(): void
    {
        $this->assertSame('150.00', $this->makePetHealthRecord()->cost);
    }

    public function test_relationship(): void
    {
        $record = $this->makePetHealthRecord();

        $this->assertInstanceOf(BelongsTo::class, $record->type());
    }
}
