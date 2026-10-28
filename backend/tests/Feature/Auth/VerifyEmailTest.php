<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Jobs\SendEmailVerificationJob;
use App\Models\EmailVerificationCode;
use App\Models\User;
use App\Notifications\Auth\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase;

    private function registerUser(): User
    {
        Queue::fake();
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Maria', 'email' => 'maria@example.com',
            'password' => 'Senha123', 'password_confirmation' => 'Senha123',
        ])->assertCreated();

        return User::where('email', 'maria@example.com')->firstOrFail();
    }

    public function test_verify_email_with_issued_code_activates_account_and_allows_login(): void
    {
        $user = $this->registerUser();
        $code = EmailVerificationCode::where('user_id', $user->id)->value('code');

        $this->postJson('/api/v1/auth/verify-email', ['email' => $user->email, 'code' => $code])
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertNotNull(EmailVerificationCode::where('user_id', $user->id)->value('verified_at'));

        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'Senha123'])
            ->assertOk()
            ->assertJsonStructure(['data' => ['token']]);
    }

    public function test_wrong_code_is_rejected_and_counts_attempt(): void
    {
        $user = $this->registerUser();
        $code = EmailVerificationCode::where('user_id', $user->id)->value('code');
        $wrong = $code === '000000' ? '111111' : '000000';

        $this->postJson('/api/v1/auth/verify-email', ['email' => $user->email, 'code' => $wrong])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Código de verificação inválido ou expirado.');

        $this->assertNull($user->fresh()->email_verified_at);
        $this->assertSame(1, (int) EmailVerificationCode::where('user_id', $user->id)->value('attempts'));
    }

    public function test_code_is_locked_after_max_attempts_even_if_correct(): void
    {
        $user = $this->registerUser();
        $record = EmailVerificationCode::where('user_id', $user->id)->firstOrFail();
        $record->update(['attempts' => 5]);

        $this->postJson('/api/v1/auth/verify-email', ['email' => $user->email, 'code' => $record->code])
            ->assertUnprocessable();

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_expired_code_is_rejected(): void
    {
        $user = $this->registerUser();
        $record = EmailVerificationCode::where('user_id', $user->id)->firstOrFail();
        $record->update(['expires_at' => now()->subMinute()]);

        $this->postJson('/api/v1/auth/verify-email', ['email' => $user->email, 'code' => $record->code])
            ->assertUnprocessable();
    }

    public function test_unknown_email_gets_the_same_error(): void
    {
        $this->postJson('/api/v1/auth/verify-email', ['email' => 'x@example.com', 'code' => '123456'])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Código de verificação inválido ou expirado.');
    }

    public function test_code_must_have_six_digits(): void
    {
        $this->postJson('/api/v1/auth/verify-email', ['email' => 'x@example.com', 'code' => '123'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('code', 'errors');
    }

    public function test_resend_issues_new_code_for_pending_user_only(): void
    {
        $user = $this->registerUser();
        $old = EmailVerificationCode::where('user_id', $user->id)->firstOrFail();
        $old->update(['attempts' => 4]);

        $this->postJson('/api/v1/auth/resend-verification-code', ['email' => $user->email])->assertOk();

        $this->assertSame(0, (int) EmailVerificationCode::where('user_id', $user->id)->value('attempts'));
        Queue::assertPushed(SendEmailVerificationJob::class, 2);
    }

    public function test_resend_is_neutral_for_unknown_or_verified_email(): void
    {
        Queue::fake();
        $verified = User::forceCreate(['name' => 'V', 'email' => 'v@example.com', 'password' => 'Senha123', 'email_verified_at' => now()]);

        $unknown = $this->postJson('/api/v1/auth/resend-verification-code', ['email' => 'nao@example.com']);
        $known = $this->postJson('/api/v1/auth/resend-verification-code', ['email' => $verified->email]);

        $unknown->assertOk();
        $this->assertSame($unknown->json('message'), $known->json('message'));
        Queue::assertNothingPushed();
        $this->assertSame(0, EmailVerificationCode::count());
    }

    public function test_job_sends_verification_notification_with_the_code(): void
    {
        Notification::fake();
        $user = User::create(['name' => 'M', 'email' => 'm@example.com', 'password' => 'Senha123']);

        (new SendEmailVerificationJob($user, '123456'))->handle();

        Notification::assertSentTo($user, VerifyEmailNotification::class, fn ($n) => $n->verificationCode === '123456');
    }
}
