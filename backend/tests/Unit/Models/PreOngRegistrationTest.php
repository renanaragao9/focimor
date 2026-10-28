<?php

namespace Tests\Unit\Models;

use App\Models\PreOngRegistration;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreOngRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function makeRegistration(array $attributes = []): PreOngRegistration
    {
        return PreOngRegistration::create(array_merge(['name' => 'Patas do Bem', 'email' => 'a@example.com', 'phone' => '85999990000', 'city' => 'Fortaleza', 'state' => 'CE'], $attributes));
    }

    public function test_can_create(): void
    {
        $record = $this->makeRegistration();

        $this->assertDatabaseHas('pre_ong_registrations', ['id' => $record->id, 'name' => 'Patas do Bem', 'status' => 'pending']);
    }

    public function test_can_read(): void
    {
        $this->assertNotNull(PreOngRegistration::find($this->makeRegistration()->id));
    }

    public function test_can_update(): void
    {
        $record = $this->makeRegistration();

        $record->update(['status' => 'contacted']);

        $this->assertDatabaseHas('pre_ong_registrations', ['id' => $record->id, 'status' => 'contacted']);
    }

    public function test_can_delete(): void
    {
        $record = $this->makeRegistration();

        $record->delete();

        $this->assertSoftDeleted('pre_ong_registrations', ['id' => $record->id]);
        $this->assertNull(PreOngRegistration::find($record->id));
    }

    public function test_relationship(): void
    {
        $this->assertInstanceOf(BelongsTo::class, $this->makeRegistration()->ong());
    }
}
