<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeTest extends TestCase
{
    use RefreshDatabase;

    public function test_me_returns_authenticated_user(): void
    {
        $user = User::create(['name' => 'Ana Tutora', 'email' => 'ana@example.com', 'password' => 'Senha123']);
        $token = $user->createToken('t')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', 'ana@example.com')
            ->assertJsonMissingPath('data.password');
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized();
    }

    public function test_me_rejects_invalid_token(): void
    {
        $this->withToken('999|token-invalido')->getJson('/api/v1/me')->assertUnauthorized();
    }

    public function test_api_without_version_prefix_does_not_exist(): void
    {
        $this->getJson('/api/me')->assertNotFound();
    }
}
