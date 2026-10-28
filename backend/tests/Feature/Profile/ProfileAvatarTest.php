<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Pet\ActsAsTutor;
use Tests\TestCase;

class ProfileAvatarTest extends TestCase
{
    use ActsAsTutor, RefreshDatabase;

    private array $headers = ['Accept' => 'application/json'];

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(config('filesystems.media'));
    }

    public function test_requires_authentication(): void
    {
        $this->post('/api/v1/me/avatar', ['avatar' => UploadedFile::fake()->image('a.jpg')], $this->headers)->assertUnauthorized();
        $this->deleteJson('/api/v1/me/avatar')->assertUnauthorized();
    }

    public function test_uploads_the_avatar_to_the_public_disk(): void
    {
        $user = $this->loginAsTutor();

        $response = $this->post('/api/v1/me/avatar', ['avatar' => UploadedFile::fake()->image('a.jpg')], $this->headers)->assertOk();

        Storage::disk(config('filesystems.media'))->assertExists($user->fresh()->avatar_path);
        $this->assertStringContainsString('avatars/', $response->json('data.avatar_url'));
        $this->getJson('/api/v1/me')->assertJsonPath('data.avatar_url', $response->json('data.avatar_url'));
    }

    public function test_replacing_removes_the_previous_file(): void
    {
        $user = $this->loginAsTutor();
        $this->post('/api/v1/me/avatar', ['avatar' => UploadedFile::fake()->image('a.jpg')], $this->headers)->assertOk();
        $first = $user->fresh()->avatar_path;

        $this->post('/api/v1/me/avatar', ['avatar' => UploadedFile::fake()->image('b.png')], $this->headers)->assertOk();

        Storage::disk(config('filesystems.media'))->assertMissing($first);
        Storage::disk(config('filesystems.media'))->assertExists($user->fresh()->avatar_path);
        $this->assertCount(1, Storage::disk(config('filesystems.media'))->files('avatars'));
    }

    public function test_rejects_invalid_files(): void
    {
        $user = $this->loginAsTutor();

        $this->post('/api/v1/me/avatar', [], $this->headers)->assertUnprocessable()->assertJsonValidationErrors('avatar', 'errors');
        $this->post('/api/v1/me/avatar', ['avatar' => UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')], $this->headers)->assertUnprocessable();
        $this->post('/api/v1/me/avatar', ['avatar' => UploadedFile::fake()->image('a.jpg')->size(3000)], $this->headers)->assertUnprocessable();

        $this->assertNull($user->fresh()->avatar_path);
    }

    public function test_removes_the_avatar_and_the_file(): void
    {
        $user = $this->loginAsTutor();
        $this->post('/api/v1/me/avatar', ['avatar' => UploadedFile::fake()->image('a.jpg')], $this->headers)->assertOk();
        $path = $user->fresh()->avatar_path;

        $this->deleteJson('/api/v1/me/avatar')->assertOk()->assertJsonPath('data.avatar_url', null);

        Storage::disk(config('filesystems.media'))->assertMissing($path);
        $this->assertNull($user->fresh()->avatar_path);
    }

    public function test_removing_without_an_avatar_is_harmless(): void
    {
        $this->loginAsTutor();

        $this->deleteJson('/api/v1/me/avatar')->assertOk()->assertJsonPath('data.avatar_url', null);
    }

    public function test_does_not_touch_the_avatar_of_other_users(): void
    {
        $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $other->update(['avatar_path' => 'avatars/outro.jpg']);
        Storage::disk(config('filesystems.media'))->put('avatars/outro.jpg', 'x');

        $this->post('/api/v1/me/avatar', ['avatar' => UploadedFile::fake()->image('a.jpg')], $this->headers)->assertOk();
        $this->deleteJson('/api/v1/me/avatar')->assertOk();

        Storage::disk(config('filesystems.media'))->assertExists('avatars/outro.jpg');
        $this->assertSame('avatars/outro.jpg', $other->fresh()->avatar_path);
    }
}
