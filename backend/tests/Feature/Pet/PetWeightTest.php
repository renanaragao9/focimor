<?php

declare(strict_types=1);

namespace Tests\Feature\Pet;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetWeightTest extends TestCase
{
    use ActsAsTutor, RefreshDatabase;

    private function payload(array $o = []): array
    {
        return array_merge(['weight_kg' => 12.5, 'height_cm' => 40, 'width_cm' => 25, 'measured_at' => '2026-01-10'], $o);
    }

    public function test_crud_cycle(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $base = "/api/v1/pets/{$pet->uuid}/weights";

        $id = $this->postJson($base, $this->payload())->assertCreated()->assertJsonPath('data.weight_kg', '12.50')->assertJsonPath('data.height_cm', '40.00')->json('data.id');

        $this->getJson($base)->assertOk()->assertJsonCount(1, 'data');
        $this->putJson("{$base}/{$id}", ['weight_kg' => 13])->assertOk()->assertJsonPath('data.weight_kg', '13.00');
        $this->deleteJson("{$base}/{$id}")->assertOk();

        $this->assertSoftDeleted('pet_weights', ['id' => $id]);
    }

    public function test_height_and_width_are_optional(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());

        $this->postJson("/api/v1/pets/{$pet->uuid}/weights", ['weight_kg' => 5, 'measured_at' => '2026-01-10'])
            ->assertCreated()
            ->assertJsonPath('data.height_cm', null);
    }

    public function test_validation(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $base = "/api/v1/pets/{$pet->uuid}/weights";

        $this->postJson($base, [])->assertUnprocessable()->assertJsonValidationErrors(['weight_kg', 'measured_at'], 'errors');
        $this->postJson($base, $this->payload(['weight_kg' => 0]))->assertUnprocessable()->assertJsonValidationErrors('weight_kg', 'errors');
        $this->postJson($base, $this->payload(['measured_at' => '2999-01-01']))->assertUnprocessable()->assertJsonValidationErrors('measured_at', 'errors');
    }

    public function test_index_is_ordered_by_measurement_date_desc_by_default(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $base = "/api/v1/pets/{$pet->uuid}/weights";
        $this->postJson($base, $this->payload(['measured_at' => '2026-01-01']));
        $this->postJson($base, $this->payload(['measured_at' => '2026-03-01']));

        $dates = collect($this->getJson($base)->json('data'))->pluck('measured_at')->all();

        $this->assertSame(['01/03/2026', '01/01/2026'], $dates);
    }

    public function test_cannot_access_weights_of_other_users_pets(): void
    {
        $this->loginAsTutor();
        $foreign = $this->makePetFor($this->makeUser('outro@example.com'));
        $weight = $foreign->weights()->create(['weight_kg' => 10, 'measured_at' => '2026-01-01']);

        $this->getJson("/api/v1/pets/{$foreign->uuid}/weights")->assertNotFound();
        $this->putJson("/api/v1/pets/{$foreign->uuid}/weights/{$weight->id}", ['weight_kg' => 99])->assertNotFound();
        $this->deleteJson("/api/v1/pets/{$foreign->uuid}/weights/{$weight->id}")->assertNotFound();
        $this->assertSame('10.00', $weight->fresh()->weight_kg);
    }
}
