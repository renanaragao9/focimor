<?php

namespace Tests\Unit\Models;

use App\Models\AdoptionSwipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdoptionSwipeTest extends TestCase
{
    use MakesPet;
    use RefreshDatabase;

    protected function makeUser(): User
    {
        return User::create(['name' => 'Adotante', 'email' => 'a'.Str::random(6).'@example.com', 'password' => 'password']);
    }

    protected function makeAdoptionSwipe(array $attributes = []): AdoptionSwipe
    {
        return AdoptionSwipe::create(array_merge(['action' => 'like', 'user_id' => $this->makeUser()->id, 'pet_id' => $this->makePet()->id], $attributes));
    }

    public function test_can_create(): void
    {
        $record = $this->makeAdoptionSwipe();

        $this->assertDatabaseHas('adoption_swipes', ['id' => $record->id, 'action' => 'like']);
    }

    public function test_can_read(): void
    {
        $this->assertNotNull(AdoptionSwipe::find($this->makeAdoptionSwipe()->id));
    }

    public function test_can_update(): void
    {
        $record = $this->makeAdoptionSwipe();

        $record->update(['action' => 'superlike']);

        $this->assertDatabaseHas('adoption_swipes', ['id' => $record->id, 'action' => 'superlike']);
    }

    public function test_can_delete(): void
    {
        $record = $this->makeAdoptionSwipe();

        $record->delete();

        $this->assertSoftDeleted('adoption_swipes', ['id' => $record->id]);
        $this->assertNull(AdoptionSwipe::find($record->id));
    }

    public function test_user_and_pet_are_unique(): void
    {
        $swipe = $this->makeAdoptionSwipe();

        $this->expectException(QueryException::class);

        AdoptionSwipe::create(['action' => 'dislike', 'user_id' => $swipe->user_id, 'pet_id' => $swipe->pet_id]);
    }

    public function test_relationship(): void
    {
        $this->assertInstanceOf(BelongsTo::class, $this->makeAdoptionSwipe()->pet());
    }
}
