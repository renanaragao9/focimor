<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\TestUserSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_master_user_can_open_the_panel(): void
    {
        $master = User::forceCreate(['name' => 'Master', 'email' => 'master@example.com', 'password' => 'Senha123', 'is_master' => true]);

        $this->actingAs($master)->get('/admin')->assertOk();
    }

    public function test_regular_user_cannot_open_the_panel_in_any_environment(): void
    {
        $user = User::create(['name' => 'Tutor', 'email' => 'tutor@example.com', 'password' => 'Senha123']);

        foreach (['testing', 'local', 'production'] as $env) {
            config(['app.env' => $env]);
            $this->actingAs($user)->get('/admin')->assertForbidden();
        }
    }

    public function test_guest_is_sent_to_the_panel_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_is_master_cannot_be_mass_assigned(): void
    {
        $user = User::create(['name' => 'Hacker', 'email' => 'h@example.com', 'password' => 'Senha123', 'is_master' => true]);

        $this->assertFalse($user->fresh()->is_master);
    }

    public function test_register_endpoint_never_creates_a_master(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Hacker', 'email' => 'h@example.com', 'is_master' => true,
            'password' => 'Senha123', 'password_confirmation' => 'Senha123',
        ])->assertCreated();

        $this->assertFalse(User::where('email', 'h@example.com')->firstOrFail()->is_master);
    }

    public function test_api_resource_does_not_expose_is_master(): void
    {
        $master = User::forceCreate(['name' => 'Master', 'email' => 'm@example.com', 'password' => 'Senha123', 'is_master' => true]);

        $this->withToken($master->createToken('t')->plainTextToken)->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonMissingPath('data.is_master');
    }

    public function test_seeders_only_make_the_admin_master(): void
    {
        $this->seed([UserSeeder::class, TestUserSeeder::class]);

        $this->assertTrue(User::where('email', 'aragaolabs@gmail.com')->firstOrFail()->is_master);
        $this->assertSame(0, User::where('email', '!=', 'aragaolabs@gmail.com')->where('is_master', true)->count());
    }

    public function test_seeder_promotes_an_existing_admin_without_recreating_it(): void
    {
        $existing = User::create(['name' => 'Admin', 'email' => 'aragaolabs@gmail.com', 'password' => 'Senha123']);

        $this->seed(UserSeeder::class);

        $this->assertSame(1, User::where('email', 'aragaolabs@gmail.com')->count());
        $this->assertTrue($existing->fresh()->is_master);
    }
}
