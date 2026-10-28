<?php

namespace Tests\Unit\Models;

use App\Models\HealthRecordType;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthRecordTypeTest extends TestCase
{
    use RefreshDatabase;

    protected function makeHealthRecordType(array $attributes = []): HealthRecordType
    {
        return HealthRecordType::create(array_merge(['name' => 'Consulta', 'slug' => 'consultation', 'order' => 1, 'is_active' => true], $attributes));
    }

    public function test_can_create(): void
    {
        $record = $this->makeHealthRecordType();

        $this->assertDatabaseHas('health_record_types', ['id' => $record->id, 'name' => 'Consulta']);
    }

    public function test_can_read(): void
    {
        $record = $this->makeHealthRecordType();

        $this->assertNotNull(HealthRecordType::find($record->id));
    }

    public function test_can_update(): void
    {
        $record = $this->makeHealthRecordType();

        $record->update(['name' => 'Exame']);

        $this->assertDatabaseHas('health_record_types', ['id' => $record->id, 'name' => 'Exame']);
    }

    public function test_can_delete(): void
    {
        $record = $this->makeHealthRecordType();

        $record->delete();

        $this->assertSoftDeleted('health_record_types', ['id' => $record->id]);
        $this->assertNull(HealthRecordType::find($record->id));
    }

    public function test_slug_is_unique(): void
    {
        $this->makeHealthRecordType();

        $this->expectException(QueryException::class);

        $this->makeHealthRecordType();
    }

    public function test_relationship(): void
    {
        $record = $this->makeHealthRecordType();

        $this->assertInstanceOf(HasMany::class, $record->healthRecords());
    }
}
