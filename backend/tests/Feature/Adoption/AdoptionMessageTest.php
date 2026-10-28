<?php

declare(strict_types=1);

namespace Tests\Feature\Adoption;

use App\Models\AdoptionMatch;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdoptionMessageTest extends TestCase
{
    use HasAdoptionFixtures, RefreshDatabase;

    private User $owner;

    private AdoptionMatch $adoption;

    private function setUpChat(string $status = 'accepted'): void
    {
        $adopter = $this->loginAsTutor('adotante@example.com');
        $this->owner = $this->makeUser('dono@example.com');
        $this->adoption = $this->makeAdoption($adopter, $this->makeAdoptionPetFor($this->owner), $status);
    }

    private function url(string $suffix = ''): string
    {
        return "/api/v1/adoptions/{$this->adoption->uuid}/messages{$suffix}";
    }

    public function test_requires_authentication(): void
    {
        $this->getJson('/api/v1/adoptions/'.fake()->uuid().'/messages')->assertUnauthorized();
        $this->postJson('/api/v1/adoptions/'.fake()->uuid().'/messages', [])->assertUnauthorized();
        $this->putJson('/api/v1/adoptions/'.fake()->uuid().'/messages/read')->assertUnauthorized();
    }

    public function test_both_parties_can_chat_when_the_interest_is_accepted(): void
    {
        $this->setUpChat();

        $response = $this->postJson($this->url(), ['body' => ' Olá, tenho interesse no Bidu '])
            ->assertCreated()
            ->assertJsonPath('data.body', 'Olá, tenho interesse no Bidu')
            ->assertJsonPath('data.is_mine', true);

        $this->assertDatabaseHas('messages', ['uuid' => $response->json('data.uuid'), 'adoption_match_id' => $this->adoption->id, 'pet_match_id' => null, 'sender_user_id' => $this->tutor->id]);

        $this->switchTo($this->owner);
        $this->postJson($this->url(), ['body' => 'Claro, quando pode vir?'])->assertCreated();
        $this->getJson($this->url())->assertOk()->assertJsonCount(2, 'data')->assertJsonPath('data.0.is_mine', true)->assertJsonPath('data.1.is_mine', false);
    }

    #[DataProvider('closedStatuses')]
    public function test_chat_is_closed_until_the_interest_is_accepted(string $status): void
    {
        $this->setUpChat($status);

        $this->getJson($this->url())->assertUnprocessable()->assertJsonPath('message', 'O chat só fica disponível depois que o dono aceita o interesse.');
        $this->postJson($this->url(), ['body' => 'oi'])->assertUnprocessable();
        $this->putJson($this->url('/read'))->assertUnprocessable();
        $this->assertSame(0, Message::count());
    }

    public static function closedStatuses(): array
    {
        return [['pending'], ['declined'], ['cancelled']];
    }

    public function test_after_adoption_the_chat_can_be_read_but_not_written(): void
    {
        $this->setUpChat('adopted');
        $this->sayInAdoption($this->owner, $this->adoption, 'Parabéns!');

        $this->getJson($this->url())->assertOk()->assertJsonCount(1, 'data');
        $this->putJson($this->url('/read'))->assertOk()->assertJsonPath('data.read', 1);
        $this->postJson($this->url(), ['body' => 'obrigado'])->assertUnprocessable();
    }

    public function test_sender_is_the_authenticated_user_and_body_is_validated(): void
    {
        $this->setUpChat();

        $this->postJson($this->url(), ['body' => 'oi', 'sender_user_id' => $this->owner->id])->assertCreated();
        $this->assertSame($this->tutor->id, Message::firstOrFail()->sender_user_id);

        $this->postJson($this->url(), [])->assertUnprocessable()->assertJsonValidationErrors('body', 'errors');
        $this->postJson($this->url(), ['body' => str_repeat('a', 2001)])->assertUnprocessable();
    }

    public function test_third_parties_cannot_read_or_write(): void
    {
        $this->setUpChat();
        $this->sayInAdoption($this->owner, $this->adoption, 'privado');
        $this->switchTo($this->makeUser('intruso@example.com'));

        $this->getJson($this->url())->assertNotFound();
        $this->postJson($this->url(), ['body' => 'intruso'])->assertNotFound();
        $this->putJson($this->url('/read'))->assertNotFound();
        $this->assertSame(1, Message::count());
    }

    public function test_read_marks_only_the_other_partys_unread_messages(): void
    {
        $this->setUpChat();
        $mine = $this->sayInAdoption($this->tutor, $this->adoption, 'minha');
        $theirs = $this->sayInAdoption($this->owner, $this->adoption, 'dele');

        $this->putJson($this->url('/read'))->assertOk()->assertJsonPath('data.read', 1);

        $this->assertNull($mine->fresh()->read_at);
        $this->assertNotNull($theirs->fresh()->read_at);
    }

    public function test_lists_newest_first_and_paginates(): void
    {
        $this->setUpChat();
        foreach (range(1, 3) as $i) {
            $this->sayInAdoption($this->tutor, $this->adoption, "m{$i}", ['created_at' => now()->subMinutes(10 - $i)]);
        }

        $this->assertSame(['m3', 'm2'], collect($this->getJson($this->url().'?per_page=2')->json('data'))->pluck('body')->all());
        $this->assertSame('m1', $this->getJson($this->url().'?direction=asc')->json('data.0.body'));
    }

    public function test_adoption_list_shows_unread_count_and_last_message(): void
    {
        $this->setUpChat();
        $this->sayInAdoption($this->owner, $this->adoption, 'primeira', ['created_at' => now()->subMinutes(2)]);
        $this->sayInAdoption($this->owner, $this->adoption, 'segunda', ['created_at' => now()->subMinute()]);

        $item = $this->getJson('/api/v1/adoptions')->assertOk()->json('data.0');

        $this->assertSame(2, $item['unread_count']);
        $this->assertSame('segunda', $item['last_message']['body']);

        $this->putJson($this->url('/read'))->assertOk();
        $this->getJson('/api/v1/adoptions')->assertJsonPath('data.0.unread_count', 0);
    }

    public function test_pet_match_chat_and_adoption_chat_do_not_mix(): void
    {
        $this->setUpChat();
        $this->sayInAdoption($this->tutor, $this->adoption, 'só da adoção');

        $this->assertSame(0, Message::whereNotNull('pet_match_id')->count());
        $this->getJson('/api/v1/matches')->assertJsonCount(0, 'data');
    }
}
