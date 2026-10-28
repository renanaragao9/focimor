<?php

declare(strict_types=1);

namespace Tests\Feature\PetMatch;

use App\Models\Goal;
use App\Models\PetMatch;
use App\Models\Swipe;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\Pet\ActsAsTutor;
use Tests\TestCase;

class PetSwipeTest extends TestCase
{
    use ActsAsTutor, RefreshDatabase;

    private function swipe(string $petUuid, string $targetUuid, string $action = 'like')
    {
        return $this->postJson("/api/v1/pets/{$petUuid}/swipes", ['target_pet_uuid' => $targetUuid, 'action' => $action]);
    }

    public function test_requires_authentication(): void
    {
        $this->seed(LookupSeeder::class);
        $pet = $this->makePetFor($this->makeUser('a@example.com'));

        $this->postJson("/api/v1/pets/{$pet->uuid}/swipes", [])->assertUnauthorized();
    }

    public function test_unilateral_like_is_recorded_without_match(): void
    {
        $mine = $this->makePetFor($this->loginAsTutor());
        $target = $this->makePetFor($this->makeUser('outro@example.com'), ['name' => 'Luna']);

        $this->swipe($mine->uuid, $target->uuid)
            ->assertCreated()
            ->assertJsonPath('data.matched', false)
            ->assertJsonPath('data.match', null)
            ->assertJsonPath('data.swipe.action', 'like')
            ->assertJsonPath('data.swipe.target_pet_uuid', $target->uuid);

        $this->assertDatabaseHas('swipes', ['swiper_pet_id' => $mine->id, 'target_pet_id' => $target->id, 'action' => 'like']);
        $this->assertSame(0, PetMatch::count());
    }

    public function test_mutual_like_creates_a_single_match_in_both_directions(): void
    {
        $user = $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $mine = $this->makePetFor($user, ['name' => 'Rex']);
        $target = $this->makePetFor($other, ['name' => 'Luna']);
        $this->swipe($mine->uuid, $target->uuid)->assertJsonPath('data.matched', false);

        $this->switchTo($other);
        $response = $this->swipe($target->uuid, $mine->uuid, 'superlike')
            ->assertCreated()
            ->assertJsonPath('data.matched', true)
            ->assertJsonPath('message', 'É um match!')
            ->assertJsonPath('data.match.my_pet.name', 'Luna')
            ->assertJsonPath('data.match.other_pet.name', 'Rex');

        $this->assertSame(1, PetMatch::count());
        $match = PetMatch::firstOrFail();
        $this->assertTrue($match->is_active);
        $this->assertLessThan($match->pet_two_id, $match->pet_one_id);
        $this->assertNotEmpty($response->json('data.match.uuid'));
        $response->assertJsonMissingPath('data.match.other_pet.latitude');
    }

    public function test_dislike_never_creates_a_match_even_if_target_liked(): void
    {
        $user = $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $mine = $this->makePetFor($user);
        $target = $this->makePetFor($other, ['name' => 'Luna']);
        Swipe::create(['action' => 'like', 'swiper_pet_id' => $target->id, 'target_pet_id' => $mine->id]);

        $this->swipe($mine->uuid, $target->uuid, 'dislike')->assertCreated()->assertJsonPath('data.matched', false);

        $this->assertSame(0, PetMatch::count());
    }

    public function test_like_after_a_dislike_from_the_target_does_not_match(): void
    {
        $user = $this->loginAsTutor();
        $mine = $this->makePetFor($user);
        $target = $this->makePetFor($this->makeUser('outro@example.com'));
        Swipe::create(['action' => 'dislike', 'swiper_pet_id' => $target->id, 'target_pet_id' => $mine->id]);

        $this->swipe($mine->uuid, $target->uuid)->assertJsonPath('data.matched', false);

        $this->assertSame(0, PetMatch::count());
    }

    public function test_cannot_swipe_twice_on_the_same_pet(): void
    {
        $mine = $this->makePetFor($this->loginAsTutor());
        $target = $this->makePetFor($this->makeUser('outro@example.com'));
        $this->swipe($mine->uuid, $target->uuid)->assertCreated();

        $this->swipe($mine->uuid, $target->uuid, 'dislike')
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Você já curtiu ou passou neste pet.');

        $this->assertSame(1, Swipe::count());
    }

    public function test_cannot_swipe_on_own_pet(): void
    {
        $user = $this->loginAsTutor();
        $mine = $this->makePetFor($user);
        $sibling = $this->makePetFor($user, ['name' => 'Irmão']);

        $this->swipe($mine->uuid, $sibling->uuid)
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Você não pode curtir um pet seu.');
    }

    public function test_cannot_swipe_with_a_pet_that_is_not_mine(): void
    {
        $this->loginAsTutor();
        $foreign = $this->makePetFor($this->makeUser('outro@example.com'));
        $target = $this->makePetFor($this->makeUser('terceiro@example.com'));

        $this->swipe($foreign->uuid, $target->uuid)->assertNotFound();
        $this->assertSame(0, Swipe::count());
    }

    public function test_adoption_pets_do_not_take_part_in_pet_matching(): void
    {
        $user = $this->loginAsTutor();
        $adoption = Goal::where('slug', 'adoption')->value('id');
        $mine = $this->makePetFor($user);
        $adoptionPet = $this->makePetFor($this->makeUser('outro@example.com'), ['goal_id' => $adoption]);
        $myAdoptionPet = $this->makePetFor($user, ['name' => 'Para Adoção', 'goal_id' => $adoption]);
        $normal = $this->makePetFor($this->makeUser('terceiro@example.com'));

        $this->swipe($mine->uuid, $adoptionPet->uuid)->assertUnprocessable();
        $this->swipe($myAdoptionPet->uuid, $normal->uuid)->assertUnprocessable();
    }

    public function test_inactive_pet_cannot_swipe_and_inactive_target_is_not_found(): void
    {
        $user = $this->loginAsTutor();
        $inactive = $this->makePetFor($user, ['is_active' => false]);
        $active = $this->makePetFor($user, ['name' => 'Ativo']);
        $target = $this->makePetFor($this->makeUser('outro@example.com'));
        $inactiveTarget = $this->makePetFor($this->makeUser('terceiro@example.com'), ['is_active' => false]);

        $this->swipe($inactive->uuid, $target->uuid)->assertUnprocessable();
        $this->swipe($active->uuid, $inactiveTarget->uuid)->assertUnprocessable()->assertJsonValidationErrors('target_pet_uuid', 'errors');
    }

    #[DataProvider('invalidPayloads')]
    public function test_validates_payload(array $payload, string $field): void
    {
        $mine = $this->makePetFor($this->loginAsTutor());

        $this->postJson("/api/v1/pets/{$mine->uuid}/swipes", $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors($field, 'errors');
    }

    public static function invalidPayloads(): array
    {
        return [
            'sem alvo' => [['action' => 'like'], 'target_pet_uuid'],
            'alvo inválido' => [['target_pet_uuid' => 'x', 'action' => 'like'], 'target_pet_uuid'],
            'alvo inexistente' => [['target_pet_uuid' => '00000000-0000-0000-0000-000000000000', 'action' => 'like'], 'target_pet_uuid'],
            'ação inválida' => [['target_pet_uuid' => '00000000-0000-0000-0000-000000000000', 'action' => 'love'], 'action'],
        ];
    }
}
