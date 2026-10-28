<?php

declare(strict_types=1);

namespace Tests\Feature\Pet;

use App\Models\HealthRecordType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetHealthRecordTest extends TestCase
{
    use ActsAsTutor, RefreshDatabase;

    private function typeId(string $slug = 'consultation'): int
    {
        return HealthRecordType::where('slug', $slug)->value('id');
    }

    private function payload(array $o = []): array
    {
        return array_merge(['health_record_type_id' => $this->typeId(), 'title' => 'Consulta anual', 'performed_at' => '2026-02-01', 'cost' => 150.5], $o);
    }

    public function test_crud_cycle_returns_type(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $base = "/api/v1/pets/{$pet->uuid}/health-records";

        $id = $this->postJson($base, $this->payload())
            ->assertCreated()
            ->assertJsonPath('data.type.name', 'Consulta')
            ->assertJsonPath('data.performed_at', '01/02/2026')
            ->json('data.id');

        $this->getJson($base)->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.type.name', 'Consulta');
        $this->putJson("{$base}/{$id}", ['health_record_type_id' => $this->typeId('exam')])->assertOk()->assertJsonPath('data.type.name', 'Exame');
        $this->deleteJson("{$base}/{$id}")->assertOk();

        $this->assertSoftDeleted('pet_health_records', ['id' => $id]);
    }

    public function test_validation(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $base = "/api/v1/pets/{$pet->uuid}/health-records";

        $this->postJson($base, [])->assertUnprocessable()->assertJsonValidationErrors(['health_record_type_id', 'title', 'performed_at'], 'errors');
        $this->postJson($base, $this->payload(['health_record_type_id' => 9999]))->assertUnprocessable()->assertJsonValidationErrors('health_record_type_id', 'errors');
        $this->postJson($base, $this->payload(['cost' => -1]))->assertUnprocessable()->assertJsonValidationErrors('cost', 'errors');
        $this->postJson($base, $this->payload(['next_due_at' => '2020-01-01']))->assertUnprocessable()->assertJsonValidationErrors('next_due_at', 'errors');
    }

    public function test_rejects_inactive_record_type(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        HealthRecordType::where('slug', 'other')->update(['is_active' => false]);

        $this->postJson("/api/v1/pets/{$pet->uuid}/health-records", $this->payload(['health_record_type_id' => $this->typeId('other')]))->assertUnprocessable();
    }

    public function test_index_filters_by_type_and_searches_title(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $base = "/api/v1/pets/{$pet->uuid}/health-records";
        $this->postJson($base, $this->payload(['title' => 'Check-up']));
        $this->postJson($base, $this->payload(['title' => 'Raio-X', 'health_record_type_id' => $this->typeId('exam')]));

        $this->getJson("{$base}?filters[health_record_type_id]={$this->typeId('exam')}")->assertJsonCount(1, 'data')->assertJsonPath('data.0.title', 'Raio-X');
        $this->getJson("{$base}?search=check")->assertJsonCount(1, 'data');
    }

    public function test_cannot_access_records_of_other_users_pets(): void
    {
        $this->loginAsTutor();
        $foreign = $this->makePetFor($this->makeUser('outro@example.com'));
        $record = $foreign->healthRecords()->create(['health_record_type_id' => $this->typeId(), 'title' => 'x', 'performed_at' => '2026-01-01']);

        $this->getJson("/api/v1/pets/{$foreign->uuid}/health-records")->assertNotFound();
        $this->putJson("/api/v1/pets/{$foreign->uuid}/health-records/{$record->id}", ['title' => 'hack'])->assertNotFound();
        $this->deleteJson("/api/v1/pets/{$foreign->uuid}/health-records/{$record->id}")->assertNotFound();
        $this->assertSame('x', $record->fresh()->title);
    }
}
