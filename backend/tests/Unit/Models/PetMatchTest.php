<?php

namespace Tests\Unit\Models;

use App\Models\Goal;
use App\Models\Message;
use App\Models\Pet;
use App\Models\PetMatch;
use App\Models\Species;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class PetMatchTest extends TestCase
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

    protected function makeMatch(array $attributes = []): PetMatch
    {
        $attributes['pet_one_id'] ??= $this->makePet('Pet One')->id;
        $attributes['pet_two_id'] ??= $this->makePet('Pet Two')->id;

        return PetMatch::create(array_merge([
            'is_active' => true,
            'matched_at' => now(),
        ], $attributes));
    }

    public function test_can_create_match(): void
    {
        $match = $this->makeMatch();

        $this->assertDatabaseHas('pet_matches', [
            'id' => $match->id,
            'is_active' => true,
        ]);
    }

    public function test_match_gets_uuid_on_creation(): void
    {
        $match = $this->makeMatch();

        $this->assertNotEmpty($match->uuid);
        $this->assertTrue(Str::isUuid($match->uuid));
    }

    public function test_match_route_key_is_uuid(): void
    {
        $this->assertSame('uuid', $this->makeMatch()->getRouteKeyName());
    }

    public function test_can_update_match(): void
    {
        $match = $this->makeMatch();

        $match->update(['is_active' => false]);

        $this->assertDatabaseHas('pet_matches', [
            'id' => $match->id,
            'is_active' => false,
        ]);
    }

    public function test_can_delete_match(): void
    {
        $match = $this->makeMatch();

        $match->delete();

        $this->assertSoftDeleted('pet_matches', ['id' => $match->id]);
        $this->assertNull(PetMatch::find($match->id));
    }

    public function test_match_casts_attributes(): void
    {
        $match = $this->makeMatch(['is_active' => 1]);

        $this->assertIsBool($match->is_active);
        $this->assertInstanceOf(Carbon::class, $match->matched_at);
    }

    public function test_match_pair_is_unique(): void
    {
        $one = $this->makePet('One')->id;
        $two = $this->makePet('Two')->id;

        $this->makeMatch(['pet_one_id' => $one, 'pet_two_id' => $two]);

        $this->expectException(QueryException::class);

        $this->makeMatch(['pet_one_id' => $one, 'pet_two_id' => $two]);
    }

    public function test_match_belongs_to_both_pets(): void
    {
        $match = $this->makeMatch();

        $this->assertInstanceOf(BelongsTo::class, $match->petOne());
        $this->assertInstanceOf(BelongsTo::class, $match->petTwo());
        $this->assertInstanceOf(Pet::class, $match->petOne()->first());
        $this->assertInstanceOf(Pet::class, $match->petTwo()->first());
    }

    public function test_match_has_latest_message_relationship(): void
    {
        $match = $this->makeMatch();

        $this->assertInstanceOf(HasOne::class, $match->latestMessage());
    }

    public function test_match_has_many_messages(): void
    {
        $match = $this->makeMatch();

        Message::create([
            'body' => 'Olá!',
            'pet_match_id' => $match->id,
            'sender_user_id' => $match->petOne()->first()->user_id,
        ]);

        $this->assertInstanceOf(HasMany::class, $match->messages());
        $this->assertCount(1, $match->messages()->get());
    }
}
