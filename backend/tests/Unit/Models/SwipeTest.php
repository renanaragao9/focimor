<?php

namespace Tests\Unit\Models;

use App\Models\Goal;
use App\Models\Pet;
use App\Models\Species;
use App\Models\Swipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SwipeTest extends TestCase
{
    use RefreshDatabase;

    protected function makePet(string $name = 'Rex'): Pet
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
            'name' => $name,
            'gender' => 'male',
            'is_active' => true,
            'user_id' => $user->id,
            'species_id' => $species->id,
            'goal_id' => $goal->id,
        ]);
    }

    protected function makeSwipe(array $attributes = []): Swipe
    {
        $attributes['swiper_pet_id'] ??= $this->makePet('Swiper')->id;
        $attributes['target_pet_id'] ??= $this->makePet('Target')->id;

        return Swipe::create(array_merge([
            'action' => 'like',
        ], $attributes));
    }

    public function test_can_create_swipe(): void
    {
        $swipe = $this->makeSwipe();

        $this->assertDatabaseHas('swipes', [
            'id' => $swipe->id,
            'action' => 'like',
        ]);
    }

    public function test_can_read_swipe(): void
    {
        $swipe = $this->makeSwipe();

        $found = Swipe::find($swipe->id);

        $this->assertNotNull($found);
        $this->assertSame('like', $found->action);
    }

    public function test_can_update_swipe(): void
    {
        $swipe = $this->makeSwipe();

        $swipe->update(['action' => 'superlike']);

        $this->assertDatabaseHas('swipes', [
            'id' => $swipe->id,
            'action' => 'superlike',
        ]);
    }

    public function test_can_delete_swipe(): void
    {
        $swipe = $this->makeSwipe();

        $swipe->delete();

        $this->assertSoftDeleted('swipes', ['id' => $swipe->id]);
        $this->assertNull(Swipe::find($swipe->id));
    }

    public function test_swipe_pair_is_unique(): void
    {
        $swiper = $this->makePet('Swiper')->id;
        $target = $this->makePet('Target')->id;

        $this->makeSwipe(['swiper_pet_id' => $swiper, 'target_pet_id' => $target]);

        $this->expectException(QueryException::class);

        $this->makeSwipe([
            'swiper_pet_id' => $swiper,
            'target_pet_id' => $target,
            'action' => 'dislike',
        ]);
    }

    public function test_swipe_belongs_to_swiper_and_target(): void
    {
        $swipe = $this->makeSwipe();

        $this->assertInstanceOf(BelongsTo::class, $swipe->swiperPet());
        $this->assertInstanceOf(BelongsTo::class, $swipe->targetPet());
        $this->assertInstanceOf(Pet::class, $swipe->swiperPet()->first());
        $this->assertInstanceOf(Pet::class, $swipe->targetPet()->first());
    }
}
