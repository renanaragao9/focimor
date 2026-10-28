<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Jobs\SendEmailVerificationJob;
use App\Models\User;
use Database\Seeders\TestUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::forceCreate(['name' => 'João Tutor', 'email' => 'joao@example.com', 'password' => 'Senha123', 'email_verified_at' => now()]);
    }

    public function test_login_returns_token_and_user(): void
    {
        $user = $this->createUser();

        $this->postJson('/api/v1/auth/login', ['email' => 'joao@example.com', 'password' => 'Senha123'])
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.me.id', $user->id)
            ->assertJsonStructure(['data' => ['token', 'me']]);

        $this->assertSame(1, $user->tokens()->count());
    }

    public function test_login_blocks_unverified_email_and_resends_code(): void
    {
        Queue::fake();
        $user = User::create(['name' => 'Novo', 'email' => 'novo@example.com', 'password' => 'Senha123']);

        $this->postJson('/api/v1/auth/login', ['email' => 'novo@example.com', 'password' => 'Senha123'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email', 'errors');

        $this->assertSame(0, $user->tokens()->count());
        Queue::assertPushed(SendEmailVerificationJob::class);
    }

    public function test_seeded_test_user_can_login(): void
    {
        $this->seed(TestUserSeeder::class);

        $this->postJson('/api/v1/auth/login', ['email' => 'maria@focimor.com', 'password' => 'password'])
            ->assertOk()
            ->assertJsonStructure(['data' => ['token']]);
    }

    public function test_login_with_wrong_password_fails_without_token(): void
    {
        $user = $this->createUser();

        $this->postJson('/api/v1/auth/login', ['email' => 'joao@example.com', 'password' => 'errada'])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Credenciais inválidas.');

        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_login_with_unknown_email_gives_same_message(): void
    {
        $this->postJson('/api/v1/auth/login', ['email' => 'ninguem@example.com', 'password' => 'Senha123'])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Credenciais inválidas.');
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->postJson('/api/v1/auth/login', [])->assertUnprocessable()->assertJsonPath('status', 'error');
    }

    public function test_login_is_rate_limited_per_email_and_ip(): void
    {
        $this->createUser();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', ['email' => 'joao@example.com', 'password' => 'errada'])->assertUnprocessable();
        }

        $this->postJson('/api/v1/auth/login', ['email' => 'joao@example.com', 'password' => 'Senha123'])->assertTooManyRequests();
    }
}
