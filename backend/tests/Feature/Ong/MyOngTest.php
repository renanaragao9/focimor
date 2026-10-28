<?php

declare(strict_types=1);

namespace Tests\Feature\Ong;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MyOngTest extends TestCase
{
    use HasOngFixtures, RefreshDatabase;

    public function test_requires_authentication(): void
    {
        $this->getJson('/api/v1/my-ongs')->assertUnauthorized();
        $this->putJson('/api/v1/my-ongs/'.fake()->uuid(), [])->assertUnauthorized();
    }

    public function test_lists_only_my_ongs_including_unverified_with_my_role(): void
    {
        $user = $this->loginAsTutor();
        $verified = $this->makeOng(['name' => 'Alfa']);
        $pending = $this->makeOng(['name' => 'Beta', 'status' => 'pending']);
        $this->makeOng(['name' => 'Alheia']);
        $this->joinOng($verified, $user, 'admin');
        $this->joinOng($pending, $user, 'member');

        $response = $this->getJson('/api/v1/my-ongs')->assertOk();

        $this->assertSame(['Alfa', 'Beta'], collect($response->json('data'))->pluck('name')->all());
        $this->assertSame(['admin', 'member'], collect($response->json('data'))->pluck('my_role')->all());
        $this->assertSame('pending', $response->json('data.1.status'));
    }

    public function test_show_is_for_members_only(): void
    {
        $user = $this->loginAsTutor();
        $mine = $this->makeOng(['cnpj' => '12.345.678/0001-90']);
        $other = $this->makeOng();
        $this->joinOng($mine, $user, 'member');

        $this->getJson("/api/v1/my-ongs/{$mine->uuid}")->assertOk()->assertJsonPath('data.cnpj', '12.345.678/0001-90')->assertJsonPath('data.my_role', 'member');
        $this->getJson("/api/v1/my-ongs/{$other->uuid}")->assertNotFound();
    }

    public function test_admin_updates_contact_and_donation_info_but_not_identity(): void
    {
        $user = $this->loginAsTutor();
        $ong = $this->makeOng(['name' => 'Patas', 'cnpj' => '11.111.111/0001-11']);
        $this->joinOng($ong, $user, 'admin');

        $this->putJson("/api/v1/my-ongs/{$ong->uuid}", [
            'description' => 'Resgatamos animais.', 'pix_key' => 'patas@pix.com', 'donation_info' => 'Ração e cobertores',
            'city' => 'Santos', 'state' => 'SP', 'name' => 'Hackeada', 'status' => 'suspended', 'cnpj' => '99', 'slug' => 'hack',
        ])->assertOk()->assertJsonPath('data.pix_key', 'patas@pix.com')->assertJsonPath('data.name', 'Patas');

        $ong->refresh();
        $this->assertSame('Resgatamos animais.', $ong->description);
        $this->assertSame('Santos', $ong->city);
        $this->assertSame('verified', $ong->status);
        $this->assertSame('11.111.111/0001-11', $ong->cnpj);
        $this->assertNotSame('hack', $ong->slug);
    }

    public function test_plain_member_cannot_update_the_ong(): void
    {
        $user = $this->loginAsTutor();
        $ong = $this->makeOng(['description' => 'original']);
        $this->joinOng($ong, $user, 'member');

        $this->putJson("/api/v1/my-ongs/{$ong->uuid}", ['description' => 'novo'])->assertForbidden();
        $this->assertSame('original', $ong->fresh()->description);
    }

    public function test_non_member_cannot_update_the_ong(): void
    {
        $this->loginAsTutor();
        $ong = $this->makeOng();

        $this->putJson("/api/v1/my-ongs/{$ong->uuid}", ['description' => 'novo'])->assertNotFound();
    }

    public function test_update_validates_fields(): void
    {
        $user = $this->loginAsTutor();
        $ong = $this->makeOng();
        $this->joinOng($ong, $user, 'admin');

        $this->putJson("/api/v1/my-ongs/{$ong->uuid}", ['email' => 'x', 'state' => 'SPX', 'latitude' => 200])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'state', 'latitude'], 'errors');
    }

    public function test_admin_uploads_logo_and_the_previous_file_is_removed(): void
    {
        Storage::fake(config('filesystems.media'));
        $user = $this->loginAsTutor();
        $ong = $this->makeOng();
        $this->joinOng($ong, $user, 'admin');

        $first = $this->post("/api/v1/my-ongs/{$ong->uuid}/logo", ['logo' => UploadedFile::fake()->image('a.png')], ['Accept' => 'application/json'])
            ->assertOk()->json('data.logo_url');
        $firstPath = $ong->fresh()->logo_path;
        Storage::disk(config('filesystems.media'))->assertExists($firstPath);
        $this->assertStringContainsString('ong-logos/', $first);

        $this->post("/api/v1/my-ongs/{$ong->uuid}/logo", ['logo' => UploadedFile::fake()->image('b.png')], ['Accept' => 'application/json'])->assertOk();

        Storage::disk(config('filesystems.media'))->assertMissing($firstPath);
        Storage::disk(config('filesystems.media'))->assertExists($ong->fresh()->logo_path);
    }

    public function test_logo_validation_and_permissions(): void
    {
        Storage::fake(config('filesystems.media'));
        $user = $this->loginAsTutor();
        $ong = $this->makeOng();
        $this->joinOng($ong, $user, 'member');
        $headers = ['Accept' => 'application/json'];

        $this->post("/api/v1/my-ongs/{$ong->uuid}/logo", ['logo' => UploadedFile::fake()->image('a.png')], $headers)->assertForbidden();

        $ong->users()->updateExistingPivot($user->id, ['role' => 'admin']);
        $this->post("/api/v1/my-ongs/{$ong->uuid}/logo", ['logo' => UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')], $headers)->assertUnprocessable();
        $this->post("/api/v1/my-ongs/{$ong->uuid}/logo", ['logo' => UploadedFile::fake()->image('a.png')->size(3000)], $headers)->assertUnprocessable();
        $this->post("/api/v1/my-ongs/{$ong->uuid}/logo", [], $headers)->assertUnprocessable()->assertJsonValidationErrors('logo', 'errors');
    }
}
