<?php

namespace Tests\Feature;

use App\Models\Pet;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetHealthTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_pet_has_health_history(): void
    {
        $this->seed(DemoSeeder::class);

        $pet = Pet::with(['detail', 'vaccines', 'healthRecords', 'weights'])->firstOrFail();

        $this->assertNotNull($pet->detail);
        $this->assertCount(1, $pet->vaccines);
        $this->assertCount(1, $pet->healthRecords);
        $this->assertCount(1, $pet->weights);
    }
}
