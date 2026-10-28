<?php

namespace Tests\Unit\Models;

use App\Models\Goal;
use App\Models\Message;
use App\Models\Pet;
use App\Models\PetMatch;
use App\Models\Species;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    protected function makeUser(): User
    {
        return User::create([
            'name' => 'Tutor',
            'email' => 'tutor'.Str::random(6).'@example.com',
            'password' => 'password',
        ]);
    }

    protected function makePet(User $user): Pet
    {
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

    protected function makeMessage(array $attributes = []): Message
    {
        if (! isset($attributes['pet_match_id']) || ! isset($attributes['sender_user_id'])) {
            $userOne = $this->makeUser();
            $userTwo = $this->makeUser();
            $match = PetMatch::create([
                'is_active' => true,
                'matched_at' => now(),
                'pet_one_id' => $this->makePet($userOne)->id,
                'pet_two_id' => $this->makePet($userTwo)->id,
            ]);

            $attributes['pet_match_id'] ??= $match->id;
            $attributes['sender_user_id'] ??= $userOne->id;
        }

        return Message::create(array_merge([
            'body' => 'Olá, tudo bem?',
        ], $attributes));
    }

    public function test_can_create_message(): void
    {
        $message = $this->makeMessage();

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'body' => 'Olá, tudo bem?',
        ]);
    }

    public function test_message_gets_uuid_on_creation(): void
    {
        $message = $this->makeMessage();

        $this->assertNotEmpty($message->uuid);
        $this->assertTrue(Str::isUuid($message->uuid));
    }

    public function test_message_route_key_is_uuid(): void
    {
        $this->assertSame('uuid', $this->makeMessage()->getRouteKeyName());
    }

    public function test_can_update_message(): void
    {
        $message = $this->makeMessage();

        $message->update(['body' => 'Mensagem editada']);

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'body' => 'Mensagem editada',
        ]);
    }

    public function test_can_mark_message_as_read(): void
    {
        $message = $this->makeMessage();

        $this->assertNull($message->read_at);

        $message->update(['read_at' => now()]);

        $this->assertNotNull($message->fresh()->read_at);
    }

    public function test_can_delete_message(): void
    {
        $message = $this->makeMessage();

        $message->delete();

        $this->assertSoftDeleted('messages', ['id' => $message->id]);
        $this->assertNull(Message::find($message->id));
    }

    public function test_message_casts_read_at(): void
    {
        $message = $this->makeMessage(['read_at' => now()]);

        $this->assertInstanceOf(Carbon::class, $message->read_at);
    }

    public function test_message_belongs_to_match_and_sender(): void
    {
        $message = $this->makeMessage();

        $this->assertInstanceOf(BelongsTo::class, $message->petMatch());
        $this->assertInstanceOf(BelongsTo::class, $message->sender());
        $this->assertInstanceOf(PetMatch::class, $message->petMatch()->first());
        $this->assertInstanceOf(User::class, $message->sender()->first());
    }
}
