<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\Auth\PasswordResetNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::forceCreate(['name' => 'Ana', 'email' => 'ana@example.com', 'password' => 'Senha123', 'email_verified_at' => now()]);
    }

    public function test_forgot_password_sends_notification_with_reset_link(): void
    {
        Notification::fake();
        $user = $this->createUser();

        $this->postJson('/api/v1/auth/forgot-password', ['email' => $user->email])->assertOk();

        Notification::assertSentTo($user, PasswordResetNotification::class, function (PasswordResetNotification $n) use ($user) {
            $url = $n->toMail($user)->actionUrl;

            return str_contains($url, '/redefinir-senha') && str_contains($url, 'token='.$n->token);
        });
    }

    public function test_forgot_password_is_neutral_for_unknown_email(): void
    {
        Notification::fake();
        $known = $this->createUser();

        $unknown = $this->postJson('/api/v1/auth/forgot-password', ['email' => 'nao@example.com']);
        $exists = $this->postJson('/api/v1/auth/forgot-password', ['email' => $known->email]);

        $unknown->assertOk();
        $this->assertSame($unknown->json('message'), $exists->json('message'));
        Notification::assertCount(1);
    }

    public function test_reset_password_changes_password_and_revokes_tokens(): void
    {
        $user = $this->createUser();
        $user->createToken('a');
        $token = Password::createToken($user);

        $this->postJson('/api/v1/auth/reset-password', [
            'token' => $token, 'email' => $user->email,
            'password' => 'NovaSenha1', 'password_confirmation' => 'NovaSenha1',
        ])->assertOk()->assertJsonPath('status', 'success');

        $this->assertTrue(Hash::check('NovaSenha1', $user->fresh()->password));
        $this->assertSame(0, $user->tokens()->count());

        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'NovaSenha1'])->assertOk();
    }

    public function test_reset_password_token_cannot_be_reused(): void
    {
        $user = $this->createUser();
        $token = Password::createToken($user);
        $payload = ['token' => $token, 'email' => $user->email, 'password' => 'NovaSenha1', 'password_confirmation' => 'NovaSenha1'];

        $this->postJson('/api/v1/auth/reset-password', $payload)->assertOk();
        $this->postJson('/api/v1/auth/reset-password', $payload)
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Token inválido ou expirado.');
    }

    public function test_reset_password_with_invalid_token_fails(): void
    {
        $user = $this->createUser();

        $this->postJson('/api/v1/auth/reset-password', [
            'token' => 'token-invalido', 'email' => $user->email,
            'password' => 'NovaSenha1', 'password_confirmation' => 'NovaSenha1',
        ])->assertUnprocessable()->assertJsonPath('status', 'error');

        $this->assertTrue(Hash::check('Senha123', $user->fresh()->password));
    }

    public function test_reset_password_validates_new_password(): void
    {
        $user = $this->createUser();
        $token = Password::createToken($user);

        $this->postJson('/api/v1/auth/reset-password', [
            'token' => $token, 'email' => $user->email,
            'password' => 'fraca', 'password_confirmation' => 'fraca',
        ])->assertUnprocessable()->assertJsonValidationErrors('password', 'errors');
    }

    public function test_forgot_password_is_rate_limited(): void
    {
        Notification::fake();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/forgot-password', ['email' => 'ana@example.com'])->assertOk();
        }

        $this->postJson('/api/v1/auth/forgot-password', ['email' => 'ana@example.com'])->assertTooManyRequests();
    }
}
