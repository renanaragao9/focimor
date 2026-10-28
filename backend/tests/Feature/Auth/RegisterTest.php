<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Jobs\SendEmailVerificationJob;
use App\Models\EmailVerificationCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Maria Tutora',
            'email' => 'maria.teste@example.com',
            'password' => 'Senha123',
            'password_confirmation' => 'Senha123',
        ], $overrides);
    }

    public function test_register_creates_unverified_user_without_token_and_sends_code(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/v1/auth/register', $this->payload());

        $response->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.requires_verification', true)
            ->assertJsonStructure(['data' => ['me' => ['id', 'name', 'email']]])
            ->assertJsonMissingPath('data.token')
            ->assertJsonMissingPath('data.me.password');

        $user = User::where('email', 'maria.teste@example.com')->firstOrFail();
        $this->assertNotSame('Senha123', $user->password);
        $this->assertNull($user->email_verified_at);
        $this->assertSame(0, $user->tokens()->count());
        $this->assertMatchesRegularExpression('/^\d{6}$/', EmailVerificationCode::where('user_id', $user->id)->value('code'));
        Queue::assertPushed(SendEmailVerificationJob::class);
    }

    public function test_register_rejects_duplicated_email(): void
    {
        Queue::fake();
        $this->postJson('/api/v1/auth/register', $this->payload())->assertCreated();

        $this->postJson('/api/v1/auth/register', $this->payload())
            ->assertUnprocessable()
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'Este e-mail já está cadastrado.');
    }

    #[DataProvider('invalidPayloads')]
    public function test_register_validates_input(array $overrides, string $field): void
    {
        $this->postJson('/api/v1/auth/register', $this->payload($overrides))
            ->assertUnprocessable()
            ->assertJsonValidationErrors($field, 'errors');

        $this->assertSame(0, User::count());
    }

    public static function invalidPayloads(): array
    {
        return [
            'sem nome' => [['name' => ''], 'name'],
            'email inválido' => [['email' => 'nao-e-email'], 'email'],
            'senha curta' => [['password' => 'Ab1', 'password_confirmation' => 'Ab1'], 'password'],
            'senha sem número' => [['password' => 'SenhaSemNum', 'password_confirmation' => 'SenhaSemNum'], 'password'],
            'confirmação diferente' => [['password_confirmation' => 'Outra123'], 'password'],
        ];
    }
}
