<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfilePasswordTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private function actAs(): string
    {
        $this->user = User::forceCreate(['name' => 'Ana', 'email' => 'ana@example.com', 'password' => 'Senha123', 'email_verified_at' => now()]);

        return $this->user->createToken('atual')->plainTextToken;
    }

    private function change(string $current = 'Senha123', string $new = 'NovaSenha1', ?string $confirmation = null)
    {
        return $this->putJson('/api/v1/me/password', ['current_password' => $current, 'password' => $new, 'password_confirmation' => $confirmation ?? $new]);
    }

    public function test_requires_authentication(): void
    {
        $this->putJson('/api/v1/me/password', [])->assertUnauthorized();
    }

    public function test_changes_the_password_and_logs_out_the_other_devices(): void
    {
        $token = $this->actAs();
        $this->user->createToken('outro-aparelho');
        $this->user->createToken('mais-um');
        $this->withToken($token);

        $this->change()->assertOk()->assertJsonPath('status', 'success');

        $this->assertTrue(Hash::check('NovaSenha1', $this->user->fresh()->password));
        $this->assertSame(1, $this->user->tokens()->count());
        $this->assertSame('atual', $this->user->tokens()->first()->name);
        $this->app['auth']->forgetGuards();
        $this->getJson('/api/v1/me')->assertOk();
    }

    public function test_new_password_works_for_login_and_the_old_one_does_not(): void
    {
        $this->withToken($this->actAs());
        $this->change()->assertOk();
        $this->app['auth']->forgetGuards();
        $this->withoutToken();

        $this->postJson('/api/v1/auth/login', ['email' => 'ana@example.com', 'password' => 'NovaSenha1'])->assertOk();
        $this->postJson('/api/v1/auth/login', ['email' => 'ana@example.com', 'password' => 'Senha123'])->assertUnprocessable();
    }

    public function test_wrong_current_password_is_rejected_and_nothing_changes(): void
    {
        $this->withToken($this->actAs());
        $this->user->createToken('outro');

        $this->change('errada')->assertUnprocessable()->assertJsonPath('message', 'A senha atual está incorreta.');

        $this->assertTrue(Hash::check('Senha123', $this->user->fresh()->password));
        $this->assertSame(2, $this->user->tokens()->count());
    }

    public function test_validates_the_new_password(): void
    {
        $this->withToken($this->actAs());

        $this->change('Senha123', 'fraca')->assertUnprocessable()->assertJsonValidationErrors('password', 'errors');
        $this->change('Senha123', 'SemNumeroAqui')->assertUnprocessable();
        $this->change('Senha123', 'NovaSenha1', 'Diferente1')->assertUnprocessable()->assertJsonPath('message', 'A confirmação da senha não confere.');
        $this->change('Senha123', 'Senha123')->assertUnprocessable()->assertJsonPath('message', 'A nova senha deve ser diferente da atual.');
        $this->putJson('/api/v1/me/password', [])->assertUnprocessable()->assertJsonValidationErrors(['current_password', 'password'], 'errors');
    }

    public function test_is_rate_limited(): void
    {
        $this->withToken($this->actAs());

        for ($i = 0; $i < 6; $i++) {
            $this->change('errada')->assertUnprocessable();
        }

        $this->change('errada')->assertTooManyRequests();
    }

    public function test_only_changes_the_authenticated_users_password(): void
    {
        $this->withToken($this->actAs());
        $other = User::create(['name' => 'Outro', 'email' => 'outro@example.com', 'password' => 'Senha123']);
        $other->createToken('x');

        $this->change()->assertOk();

        $this->assertTrue(Hash::check('Senha123', $other->fresh()->password));
        $this->assertSame(1, $other->tokens()->count());
    }
}
