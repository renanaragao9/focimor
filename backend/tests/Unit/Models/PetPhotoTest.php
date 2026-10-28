<?php

namespace Tests\Unit\Models;

use App\Models\Goal;
use App\Models\Pet;
use App\Models\PetPhoto;
use App\Models\Species;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class PetPhotoTest extends TestCase
{
    use RefreshDatabase;

    protected function makePet(): Pet
    {
        $user = User::create([
            'name' => 'Tutor',
            'email' => 'tutor'.Str::random(6).'@example.com',
            'password' => 'password',
        ]);

        $species = Species::create([
            'name' => 'Cachorro', 'slug' => 'dog-'.Str::random(5), 'order' => 1, 'is_active' => true,
        ]);

        $goal = Goal::create([
            'name' => 'Amizade', 'slug' => 'friendship-'.Str::random(5), 'order' => 1, 'is_active' => true,
        ]);

        return Pet::create([
            'name' => 'Rex',
            'gender' => 'male',
            'is_active' => true,
            'user_id' => $user->id,
            'species_id' => $species->id,
            'goal_id' => $goal->id,
        ]);
    }

    protected function makePhoto(array $attributes = []): PetPhoto
    {
        return PetPhoto::create(array_merge([
            'path' => 'pet-photos/foto.jpg',
            'order' => 1,
            'is_primary' => true,
            'pet_id' => $this->makePet()->id,
        ], $attributes));
    }

    public function test_can_create_pet_photo(): void
    {
        $photo = $this->makePhoto();

        $this->assertDatabaseHas('pet_photos', [
            'id' => $photo->id,
            'path' => 'pet-photos/foto.jpg',
            'is_primary' => true,
        ]);
    }

    public function test_can_read_pet_photo(): void
    {
        $photo = $this->makePhoto();

        $found = PetPhoto::find($photo->id);

        $this->assertNotNull($found);
        $this->assertSame('pet-photos/foto.jpg', $found->path);
    }

    public function test_can_update_pet_photo(): void
    {
        $photo = $this->makePhoto();

        $photo->update(['order' => 5, 'is_primary' => false]);

        $this->assertDatabaseHas('pet_photos', [
            'id' => $photo->id,
            'order' => 5,
            'is_primary' => false,
        ]);
    }

    public function test_can_delete_pet_photo(): void
    {
        $photo = $this->makePhoto();

        $photo->delete();

        $this->assertSoftDeleted('pet_photos', ['id' => $photo->id]);
        $this->assertNull(PetPhoto::find($photo->id));
    }

    public function test_pet_photo_casts_attributes(): void
    {
        $photo = $this->makePhoto(['order' => '3', 'is_primary' => 0]);

        $this->assertIsInt($photo->order);
        $this->assertIsBool($photo->is_primary);
    }

    public function test_photo_uses_a_temporary_url_when_the_media_disk_supports_it(): void
    {
        config()->set('filesystems.media', 's3');
        $disk = Mockery::mock(FilesystemAdapter::class);
        Storage::shouldReceive('disk')->once()->with('s3')->andReturn($disk);
        $disk->shouldReceive('providesTemporaryUrls')->once()->andReturn(true);
        $disk->shouldReceive('temporaryUrl')
            ->once()
            ->with('pet-photos/foto.jpg', Mockery::type(CarbonInterface::class))
            ->andReturn('https://signed.example/pet-photos/foto.jpg');

        $this->assertSame('https://signed.example/pet-photos/foto.jpg', $this->makePhoto()->url);
    }

    public function test_pet_photo_belongs_to_pet(): void
    {
        $photo = $this->makePhoto();

        $this->assertInstanceOf(BelongsTo::class, $photo->pet());
        $this->assertInstanceOf(Pet::class, $photo->pet()->first());
    }
}
