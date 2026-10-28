<?php

declare(strict_types=1);

namespace Tests\Feature\Pet;

use App\Models\PetVaccine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetVaccineTest extends TestCase
{
    use ActsAsTutor, RefreshDatabase;

    private function payload(array $o = []): array
    {
        return array_merge(['name' => 'V10', 'applied_at' => '2026-01-10', 'next_dose_at' => '2027-01-10'], $o);
    }

    public function test_crud_cycle(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $base = "/api/v1/pets/{$pet->uuid}/vaccines";

        $id = $this->postJson($base, $this->payload())->assertCreated()->assertJsonPath('data.applied_at', '10/01/2026')->json('data.id');

        $this->getJson($base)->assertOk()->assertJsonCount(1, 'data');
        $this->putJson("{$base}/{$id}", ['name' => 'Antirrábica'])->assertOk()->assertJsonPath('data.name', 'Antirrábica')->assertJsonPath('data.batch', null);
        $this->deleteJson("{$base}/{$id}")->assertOk();

        $this->assertSoftDeleted('pet_vaccines', ['id' => $id]);
        $this->getJson($base)->assertJsonCount(0, 'data');
    }

    public function test_validation(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $base = "/api/v1/pets/{$pet->uuid}/vaccines";

        $this->postJson($base, [])->assertUnprocessable()->assertJsonValidationErrors(['name', 'applied_at'], 'errors');
        $this->postJson($base, $this->payload(['applied_at' => '2999-01-01']))->assertUnprocessable()->assertJsonValidationErrors('applied_at', 'errors');
        $this->postJson($base, $this->payload(['next_dose_at' => '2025-01-01']))->assertUnprocessable()->assertJsonValidationErrors('next_dose_at', 'errors');
    }

    public function test_update_validates_next_dose_against_existing_application_date(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $base = "/api/v1/pets/{$pet->uuid}/vaccines";
        $id = $this->postJson($base, $this->payload())->json('data.id');

        $this->putJson("{$base}/{$id}", ['next_dose_at' => '2025-12-31'])->assertUnprocessable();
        $this->putJson("{$base}/{$id}", ['next_dose_at' => '2027-06-01'])->assertOk();
    }

    public function test_index_search_sort_and_pagination(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $base = "/api/v1/pets/{$pet->uuid}/vaccines";
        $this->postJson($base, $this->payload(['name' => 'Gripe', 'applied_at' => '2026-03-01', 'next_dose_at' => null]));
        $this->postJson($base, $this->payload(['name' => 'V10', 'applied_at' => '2026-01-01']));

        $this->getJson("{$base}?search=gri")->assertJsonCount(1, 'data');
        $names = collect($this->getJson("{$base}?sort=name&direction=asc")->json('data'))->pluck('name')->all();
        $this->assertSame(['Gripe', 'V10'], $names);
        $this->getJson("{$base}?per_page=1")->assertJsonCount(1, 'data')->assertJsonPath('meta.total', 2);
        $this->getJson("{$base}?per_page=1000")->assertUnprocessable();
    }

    public function test_cannot_access_vaccines_of_other_users_pets_or_other_pets(): void
    {
        $user = $this->loginAsTutor();
        $mine = $this->makePetFor($user);
        $foreign = $this->makePetFor($this->makeUser('outro@example.com'));
        $vaccine = $foreign->vaccines()->create(['name' => 'V10', 'applied_at' => '2026-01-10']);

        $this->getJson("/api/v1/pets/{$foreign->uuid}/vaccines")->assertNotFound();
        $this->postJson("/api/v1/pets/{$foreign->uuid}/vaccines", $this->payload())->assertNotFound();
        $this->putJson("/api/v1/pets/{$foreign->uuid}/vaccines/{$vaccine->id}", ['name' => 'x'])->assertNotFound();
        $this->putJson("/api/v1/pets/{$mine->uuid}/vaccines/{$vaccine->id}", ['name' => 'x'])->assertNotFound();
        $this->deleteJson("/api/v1/pets/{$mine->uuid}/vaccines/{$vaccine->id}")->assertNotFound();

        $this->assertSame('V10', PetVaccine::find($vaccine->id)->name);
    }
}
