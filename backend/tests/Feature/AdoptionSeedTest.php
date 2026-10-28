<?php

namespace Tests\Feature;

use App\Models\AdoptionMatch;
use App\Models\AdoptionSwipe;
use App\Models\Message;
use App\Models\Ong;
use App\Models\OngPost;
use App\Models\Pet;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdoptionSeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeders_are_idempotent_and_linked(): void
    {
        $this->seed(DemoSeeder::class);
        $this->seed(DemoSeeder::class);

        $this->assertSame(3, Ong::count());
        $this->assertSame(15, Pet::whereNotNull('ong_id')->count());
        $this->assertSame(8, OngPost::count());
        $this->assertSame(1, OngPost::whereNull('ong_id')->count());
        $this->assertSame(4, AdoptionSwipe::count());
        $this->assertSame(3, AdoptionMatch::count());
        $this->assertSame(2, Message::whereNotNull('adoption_match_id')->count());
    }

    public function test_production_seed_has_only_catalogs_and_the_admin(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(1, User::count());
        $this->assertTrue(User::firstOrFail()->is_master);
        $this->assertSame(0, Ong::count());
        $this->assertSame(0, Pet::count());
        $this->assertSame(0, OngPost::count());
    }
}
