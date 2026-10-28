<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\Pet\ActsAsTutor;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use ActsAsTutor, RefreshDatabase;

    public function test_requires_authentication(): void
    {
        $this->putJson('/api/v1/me', ['name' => 'X'])->assertUnauthorized();
    }

    public function test_me_exposes_avatar_and_location_fields(): void
    {
        $this->loginAsTutor();

        $this->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('data.avatar_url', null)
            ->assertJsonPath('data.latitude', null)
            ->assertJsonPath('data.longitude', null)
            ->assertJsonMissingPath('data.password')
            ->assertJsonMissingPath('data.is_master');
    }

    public function test_updates_name_and_location(): void
    {
        $user = $this->loginAsTutor();

        $this->putJson('/api/v1/me', ['name' => 'Maria Tutora', 'latitude' => -23.5505, 'longitude' => -46.6333])
            ->assertOk()
            ->assertJsonPath('data.name', 'Maria Tutora')
            ->assertJsonPath('data.latitude', '-23.5505000')
            ->assertJsonPath('data.longitude', '-46.6333000');

        $this->assertSame('Maria Tutora', $user->fresh()->name);
        $this->getJson('/api/v1/me')->assertJsonPath('data.latitude', '-23.5505000');
    }

    public function test_partial_update_keeps_the_other_fields(): void
    {
        $user = $this->loginAsTutor();
        $user->update(['latitude' => -10.5, 'longitude' => -40.5]);

        $this->putJson('/api/v1/me', ['name' => 'Novo Nome'])->assertOk();

        $this->assertSame('Novo Nome', $user->fresh()->name);
        $this->assertEquals(-10.5, $user->fresh()->latitude);
    }

    public function test_location_can_be_cleared(): void
    {
        $user = $this->loginAsTutor();
        $user->update(['latitude' => -10.5, 'longitude' => -40.5]);

        $this->putJson('/api/v1/me', ['latitude' => null, 'longitude' => null])->assertOk()->assertJsonPath('data.latitude', null);

        $this->assertNull($user->fresh()->latitude);
        $this->assertNull($user->fresh()->longitude);
    }

    public function test_cannot_change_email_password_or_master_flag_through_this_endpoint(): void
    {
        $user = $this->loginAsTutor();
        $originalPassword = $user->password;

        $this->putJson('/api/v1/me', ['name' => 'Ok', 'email' => 'novo@example.com', 'password' => 'Hack1234', 'is_master' => true, 'email_verified_at' => null, 'avatar_path' => 'x.jpg'])->assertOk();

        $fresh = $user->fresh();
        $this->assertSame('tutor@example.com', $fresh->email);
        $this->assertSame($originalPassword, $fresh->password);
        $this->assertFalse($fresh->is_master);
        $this->assertNull($fresh->avatar_path);
    }

    #[DataProvider('invalidPayloads')]
    public function test_validates_input(array $payload, string $field): void
    {
        $this->loginAsTutor();

        $this->putJson('/api/v1/me', $payload)->assertUnprocessable()->assertJsonValidationErrors($field, 'errors');
    }

    public static function invalidPayloads(): array
    {
        return [
            'nome vazio' => [['name' => ''], 'name'],
            'nome longo' => [['name' => str_repeat('a', 256)], 'name'],
            'latitude fora' => [['latitude' => 120, 'longitude' => 0], 'latitude'],
            'longitude fora' => [['latitude' => 0, 'longitude' => 200], 'longitude'],
            'só latitude' => [['latitude' => -23.5], 'longitude'],
            'só longitude' => [['longitude' => -46.6], 'latitude'],
            'latitude texto' => [['latitude' => 'abc', 'longitude' => 0], 'latitude'],
        ];
    }

    public function test_only_updates_the_authenticated_user(): void
    {
        $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');

        $this->putJson('/api/v1/me', ['name' => 'Alterado'])->assertOk();

        $this->assertSame('Tutor', $other->fresh()->name);
        $this->assertInstanceOf(User::class, $other);
    }
}
