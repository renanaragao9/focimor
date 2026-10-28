<?php

declare(strict_types=1);

namespace Tests\Feature\Pet;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetDetailTest extends TestCase
{
    use ActsAsTutor, RefreshDatabase;

    public function test_show_returns_null_when_pet_has_no_detail_yet(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());

        $this->getJson("/api/v1/pets/{$pet->uuid}/detail")->assertOk()->assertJsonPath('data', null);
    }

    public function test_update_creates_then_updates_the_single_detail(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());

        $this->putJson("/api/v1/pets/{$pet->uuid}/detail", ['microchip' => '123', 'temperament' => 'Dócil'])
            ->assertOk()
            ->assertJsonPath('data.microchip', '123');

        $this->putJson("/api/v1/pets/{$pet->uuid}/detail", ['microchip' => '123', 'temperament' => 'Agitado'])->assertOk();

        $this->assertSame(1, $pet->detail()->count());
        $this->getJson("/api/v1/pets/{$pet->uuid}/detail")->assertJsonPath('data.temperament', 'Agitado');
    }

    public function test_microchip_must_be_unique_across_pets(): void
    {
        $user = $this->loginAsTutor();
        $one = $this->makePetFor($user);
        $two = $this->makePetFor($user, ['name' => 'Luna']);
        $this->putJson("/api/v1/pets/{$one->uuid}/detail", ['microchip' => '999'])->assertOk();

        $this->putJson("/api/v1/pets/{$two->uuid}/detail", ['microchip' => '999'])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Este microchip já está cadastrado em outro pet.');
    }

    public function test_pet_show_includes_detail_when_present(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $this->putJson("/api/v1/pets/{$pet->uuid}/detail", ['coat' => 'Curta'])->assertOk();

        $this->getJson("/api/v1/pets/{$pet->uuid}")->assertJsonPath('data.detail.coat', 'Curta');
    }

    public function test_cannot_read_or_write_detail_of_other_users_pets(): void
    {
        $this->loginAsTutor();
        $foreign = $this->makePetFor($this->makeUser('outro@example.com'));

        $this->getJson("/api/v1/pets/{$foreign->uuid}/detail")->assertNotFound();
        $this->putJson("/api/v1/pets/{$foreign->uuid}/detail", ['coat' => 'x'])->assertNotFound();
        $this->assertSame(0, $foreign->detail()->count());
    }
}
