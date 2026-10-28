<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::create(['name' => 'Usuário', 'email' => Str::random(8).'@example.com', 'password' => 'Senha123']);
    }

    private function postWithToken(string $uri, string $token): TestResponse
    {
        return $this->withToken($token)->postJson($uri);
    }

    public function test_logout_revokes_only_the_current_token(): void
    {
        $user = $this->createUser();
        $first = $user->createToken('app-ios')->plainTextToken;
        $second = $user->createToken('app-android')->plainTextToken;

        $this->postWithToken('/api/v1/auth/logout', $first)->assertOk();

        $this->assertSame(1, $user->tokens()->count());
        $this->assertSame('app-android', $user->tokens()->first()->name);

        $this->app['auth']->forgetGuards();
        $this->postWithToken('/api/v1/auth/logout', $first)->assertUnauthorized();

        $this->app['auth']->forgetGuards();
        $this->withToken($second)->getJson('/api/v1/me')->assertOk();
    }

    public function test_logout_does_not_touch_tokens_of_other_users(): void
    {
        $user = $this->createUser();
        $other = $this->createUser();
        $token = $user->createToken('a')->plainTextToken;
        $other->createToken('b');

        $this->postWithToken('/api/v1/auth/logout', $token)->assertOk();

        $this->assertSame(0, $user->tokens()->count());
        $this->assertSame(1, $other->tokens()->count());
    }

    public function test_logout_from_all_devices_revokes_every_token_of_the_user(): void
    {
        $user = $this->createUser();
        $other = $this->createUser();
        $token = $user->createToken('a')->plainTextToken;
        $user->createToken('b');
        $other->createToken('c');

        $this->postWithToken('/api/v1/auth/logout-all-devices', $token)->assertOk();

        $this->assertSame(0, $user->tokens()->count());
        $this->assertSame(1, $other->tokens()->count());
    }

    public function test_logout_requires_authentication(): void
    {
        $this->postJson('/api/v1/auth/logout')->assertUnauthorized();
        $this->postJson('/api/v1/auth/logout-all-devices')->assertUnauthorized();
    }
}
