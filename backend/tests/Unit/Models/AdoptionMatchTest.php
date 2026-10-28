<?php

namespace Tests\Unit\Models;

use App\Models\AdoptionMatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdoptionMatchTest extends TestCase
{
    use MakesPet;
    use RefreshDatabase;

    protected function makeUser(): User
    {
        return User::create(['name' => 'Adotante', 'email' => 'a'.Str::random(6).'@example.com', 'password' => 'password']);
    }

    protected function makeAdoptionMatch(array $attributes = []): AdoptionMatch
    {
        return AdoptionMatch::create(array_merge(['user_id' => $this->makeUser()->id, 'pet_id' => $this->makePet()->id], $attributes));
    }

    public function test_can_create(): void
    {
        $record = $this->makeAdoptionMatch();

        $this->assertDatabaseHas('adoption_matches', ['id' => $record->id, 'status' => 'pending']);
    }

    public function test_can_read(): void
    {
        $this->assertNotNull(AdoptionMatch::find($this->makeAdoptionMatch()->id));
    }

    public function test_can_update(): void
    {
        $record = $this->makeAdoptionMatch();

        $record->update(['status' => 'accepted']);

        $this->assertDatabaseHas('adoption_matches', ['id' => $record->id, 'status' => 'accepted']);
    }

    public function test_can_delete(): void
    {
        $record = $this->makeAdoptionMatch();

        $record->delete();

        $this->assertSoftDeleted('adoption_matches', ['id' => $record->id]);
        $this->assertNull(AdoptionMatch::find($record->id));
    }

    public function test_status_transitions_are_defined_in_one_place(): void
    {
        $match = $this->makeAdoptionMatch()->fresh();

        $this->assertTrue($match->canTransitionTo('accepted'));
        $this->assertTrue($match->canTransitionTo('declined'));
        $this->assertTrue($match->canTransitionTo('cancelled'));
        $this->assertFalse($match->canTransitionTo('adopted'));
        $this->assertFalse($match->canTransitionTo('pending'));

        $match->status = 'adopted';
        $this->assertFalse($match->canTransitionTo('declined'));
    }

    public function test_has_latest_message_relationship(): void
    {
        $this->assertInstanceOf(HasOne::class, $this->makeAdoptionMatch()->latestMessage());
    }

    public function test_default_status_is_pending(): void
    {
        $this->assertSame('pending', $this->makeAdoptionMatch()->fresh()->status);
    }

    public function test_has_messages_relationship(): void
    {
        $this->assertInstanceOf(HasMany::class, $this->makeAdoptionMatch()->messages());
    }

    public function test_relationship(): void
    {
        $this->assertInstanceOf(BelongsTo::class, $this->makeAdoptionMatch()->pet());
    }
}
