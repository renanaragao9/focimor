<?php

declare(strict_types=1);

namespace Tests\Feature\Pet;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PetPhotoTest extends TestCase
{
    use ActsAsTutor, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(config('filesystems.media'));
    }

    private function upload(string $uuid, array $extra = [], ?UploadedFile $file = null)
    {
        return $this->post("/api/v1/pets/{$uuid}/photos", array_merge(['photo' => $file ?? UploadedFile::fake()->image('foto.jpg')], $extra), ['Accept' => 'application/json']);
    }

    public function test_first_photo_is_primary_and_stored_on_public_disk(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());

        $response = $this->upload($pet->uuid)->assertCreated()->assertJsonPath('data.is_primary', true);

        Storage::disk(config('filesystems.media'))->assertExists($pet->photos()->first()->path);
        $this->assertStringContainsString('pet-photos/', $response->json('data.url'));
    }

    public function test_photo_stored_as_an_https_link_is_returned_as_is(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $pet->photos()->create(['path' => 'https://images.dog.ceo/breeds/pug/n1.jpg', 'order' => 0, 'is_primary' => true]);

        $this->getJson("/api/v1/pets/{$pet->uuid}")->assertOk()->assertJsonPath('data.photos.0.url', 'https://images.dog.ceo/breeds/pug/n1.jpg');
    }

    public function test_new_primary_photo_demotes_the_previous_one(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $this->upload($pet->uuid)->assertCreated();

        $second = $this->upload($pet->uuid, ['is_primary' => true])->assertCreated();

        $this->assertSame(1, $pet->photos()->where('is_primary', true)->count());
        $this->assertTrue($pet->photos()->findOrFail($second->json('data.id'))->is_primary);
    }

    public function test_second_photo_is_not_primary_and_gets_next_order(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $this->upload($pet->uuid)->assertCreated();

        $this->upload($pet->uuid)->assertCreated()->assertJsonPath('data.is_primary', false)->assertJsonPath('data.order', 1);
    }

    public function test_rejects_non_image_and_oversized_files(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());

        $this->upload($pet->uuid, [], UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf'))->assertUnprocessable()->assertJsonValidationErrors('photo', 'errors');
        $this->upload($pet->uuid, [], UploadedFile::fake()->image('grande.jpg')->size(6000))->assertUnprocessable();
        $this->assertSame(0, $pet->photos()->count());
    }

    public function test_limits_photos_per_pet(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());

        for ($i = 0; $i < 10; $i++) {
            $this->upload($pet->uuid)->assertCreated();
        }

        $this->upload($pet->uuid)->assertUnprocessable()->assertJsonPath('message', 'O pet já tem o máximo de 10 fotos.');
    }

    public function test_index_lists_photos_in_order(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $this->upload($pet->uuid, ['order' => 5])->assertCreated();
        $this->upload($pet->uuid, ['order' => 1])->assertCreated();

        $orders = collect($this->getJson("/api/v1/pets/{$pet->uuid}/photos")->assertOk()->json('data'))->pluck('order')->all();

        $this->assertSame([1, 5], $orders);
    }

    public function test_update_sets_primary_and_order(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $this->upload($pet->uuid)->assertCreated();
        $second = $this->upload($pet->uuid)->assertCreated()->json('data.id');

        $this->putJson("/api/v1/pets/{$pet->uuid}/photos/{$second}", ['is_primary' => true, 'order' => 0])
            ->assertOk()
            ->assertJsonPath('data.is_primary', true);

        $this->assertSame(1, $pet->photos()->where('is_primary', true)->count());
    }

    public function test_deleting_primary_promotes_the_next_photo(): void
    {
        $pet = $this->makePetFor($this->loginAsTutor());
        $first = $this->upload($pet->uuid)->json('data.id');
        $this->upload($pet->uuid);

        $this->deleteJson("/api/v1/pets/{$pet->uuid}/photos/{$first}")->assertOk();

        $this->assertSoftDeleted('pet_photos', ['id' => $first]);
        $this->assertSame(1, $pet->photos()->where('is_primary', true)->count());
    }

    public function test_cannot_touch_photos_of_other_users_pets_or_other_pets(): void
    {
        $user = $this->loginAsTutor();
        $mine = $this->makePetFor($user);
        $other = $this->makeUser('outro@example.com');
        $foreign = $this->makePetFor($other);
        $foreignPhoto = $foreign->photos()->create(['path' => 'pet-photos/x.jpg', 'order' => 0, 'is_primary' => true]);

        $this->upload($foreign->uuid)->assertNotFound();
        $this->getJson("/api/v1/pets/{$foreign->uuid}/photos")->assertNotFound();
        $this->deleteJson("/api/v1/pets/{$foreign->uuid}/photos/{$foreignPhoto->id}")->assertNotFound();
        $this->deleteJson("/api/v1/pets/{$mine->uuid}/photos/{$foreignPhoto->id}")->assertNotFound();

        $this->assertNull($foreignPhoto->fresh()->deleted_at);
    }
}
