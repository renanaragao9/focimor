<?php

declare(strict_types=1);

namespace Tests\Feature\PetMatch;

use App\Models\Message;
use App\Models\Pet;
use App\Models\PetMatch;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Pet\ActsAsTutor;
use Tests\TestCase;

class PetMessageTest extends TestCase
{
    use ActsAsTutor, RefreshDatabase;

    private User $other;

    private PetMatch $match;

    private function setUpMatch(): void
    {
        $user = $this->loginAsTutor();
        $this->other = $this->makeUser('outro@example.com');
        $this->match = $this->makeMatch($this->makePetFor($user, ['name' => 'Rex']), $this->makePetFor($this->other, ['name' => 'Luna']));
    }

    private function makeMatch(Pet $a, Pet $b): PetMatch
    {
        [$one, $two] = $a->id < $b->id ? [$a, $b] : [$b, $a];

        return PetMatch::create(['pet_one_id' => $one->id, 'pet_two_id' => $two->id, 'is_active' => true, 'matched_at' => now()]);
    }

    private function say(User $sender, string $body, ?PetMatch $match = null, array $extra = []): Message
    {
        return Message::create(array_merge(['body' => $body, 'pet_match_id' => ($match ?? $this->match)->id, 'sender_user_id' => $sender->id], $extra));
    }

    public function test_requires_authentication(): void
    {
        $this->seed(LookupSeeder::class);
        $match = $this->makeMatch($this->makePetFor($this->makeUser('a@example.com')), $this->makePetFor($this->makeUser('b@example.com')));

        $this->getJson("/api/v1/matches/{$match->uuid}/messages")->assertUnauthorized();
        $this->postJson("/api/v1/matches/{$match->uuid}/messages", ['body' => 'oi'])->assertUnauthorized();
        $this->putJson("/api/v1/matches/{$match->uuid}/messages/read")->assertUnauthorized();
    }

    public function test_send_message_as_the_authenticated_user(): void
    {
        $this->setUpMatch();

        $response = $this->postJson("/api/v1/matches/{$this->match->uuid}/messages", ['body' => '  Vamos passear?  '])
            ->assertCreated()
            ->assertJsonPath('data.body', 'Vamos passear?')
            ->assertJsonPath('data.is_mine', true)
            ->assertJsonPath('data.read_at', null)
            ->assertJsonMissingPath('data.id')
            ->assertJsonMissingPath('data.sender_user_id');

        $this->assertDatabaseHas('messages', ['uuid' => $response->json('data.uuid'), 'pet_match_id' => $this->match->id, 'sender_user_id' => $this->tutor->id]);
    }

    public function test_sender_id_sent_by_the_client_is_ignored(): void
    {
        $this->setUpMatch();

        $this->postJson("/api/v1/matches/{$this->match->uuid}/messages", ['body' => 'oi', 'sender_user_id' => $this->other->id, 'read_at' => now()->toDateTimeString()])->assertCreated();

        $message = Message::firstOrFail();
        $this->assertSame($this->tutor->id, $message->sender_user_id);
        $this->assertNull($message->read_at);
    }

    public function test_validates_body(): void
    {
        $this->setUpMatch();
        $url = "/api/v1/matches/{$this->match->uuid}/messages";

        $this->postJson($url, [])->assertUnprocessable()->assertJsonValidationErrors('body', 'errors');
        $this->postJson($url, ['body' => '   '])->assertUnprocessable()->assertJsonValidationErrors('body', 'errors');
        $this->postJson($url, ['body' => str_repeat('a', 2001)])->assertUnprocessable()->assertJsonPath('message', 'A mensagem não pode ter mais de 2000 caracteres.');
        $this->assertSame(0, Message::count());
    }

    public function test_lists_messages_newest_first_marking_which_are_mine(): void
    {
        $this->setUpMatch();
        $this->say($this->tutor, 'primeira', null, ['created_at' => now()->subMinutes(3)]);
        $this->say($this->other, 'segunda', null, ['created_at' => now()->subMinutes(2)]);
        $this->say($this->tutor, 'terceira', null, ['created_at' => now()->subMinute()]);

        $response = $this->getJson("/api/v1/matches/{$this->match->uuid}/messages")->assertOk();

        $this->assertSame(['terceira', 'segunda', 'primeira'], collect($response->json('data'))->pluck('body')->all());
        $this->assertSame([true, false, true], collect($response->json('data'))->pluck('is_mine')->all());
        $response->assertJsonPath('meta.total', 3);

        $asc = $this->getJson("/api/v1/matches/{$this->match->uuid}/messages?direction=asc")->json('data.0.body');
        $this->assertSame('primeira', $asc);
    }

    public function test_paginates_messages(): void
    {
        $this->setUpMatch();
        foreach (range(1, 5) as $i) {
            $this->say($this->tutor, "msg {$i}");
        }

        $this->getJson("/api/v1/matches/{$this->match->uuid}/messages?per_page=2&page=3")->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('meta.total', 5);
        $this->getJson("/api/v1/matches/{$this->match->uuid}/messages?per_page=1000")->assertUnprocessable();
    }

    public function test_other_side_sees_the_same_conversation_with_inverted_ownership(): void
    {
        $this->setUpMatch();
        $this->say($this->tutor, 'oi, Luna');

        $this->switchTo($this->other);

        $this->getJson("/api/v1/matches/{$this->match->uuid}/messages")->assertOk()->assertJsonPath('data.0.is_mine', false);
    }

    public function test_messages_are_isolated_per_match(): void
    {
        $this->setUpMatch();
        $third = $this->makeUser('terceiro@example.com');
        $second = $this->makeMatch($this->tutor->pets()->first(), $this->makePetFor($third, ['name' => 'Mimi']));
        $this->say($this->tutor, 'para a Luna');
        $this->say($this->tutor, 'para a Mimi', $second);

        $this->getJson("/api/v1/matches/{$this->match->uuid}/messages")->assertJsonCount(1, 'data')->assertJsonPath('data.0.body', 'para a Luna');
    }

    public function test_cannot_read_or_write_in_a_match_that_is_not_mine(): void
    {
        $this->setUpMatch();
        $foreign = $this->makeMatch($this->makePetFor($this->makeUser('a@example.com'), ['name' => 'A']), $this->makePetFor($this->makeUser('b@example.com'), ['name' => 'B']));
        $this->say($this->other, 'segredo', $foreign);

        $this->getJson("/api/v1/matches/{$foreign->uuid}/messages")->assertNotFound();
        $this->postJson("/api/v1/matches/{$foreign->uuid}/messages", ['body' => 'intruso'])->assertNotFound();
        $this->putJson("/api/v1/matches/{$foreign->uuid}/messages/read")->assertNotFound();
        $this->assertSame(1, Message::count());
    }

    public function test_cannot_chat_after_unmatch(): void
    {
        $this->setUpMatch();
        $this->match->update(['is_active' => false]);

        $this->getJson("/api/v1/matches/{$this->match->uuid}/messages")->assertNotFound();
        $this->postJson("/api/v1/matches/{$this->match->uuid}/messages", ['body' => 'oi'])->assertNotFound();
    }

    public function test_read_marks_only_the_other_users_unread_messages(): void
    {
        $this->setUpMatch();
        $mine = $this->say($this->tutor, 'minha');
        $theirs = $this->say($this->other, 'dela');
        $alreadyRead = $this->say($this->other, 'antiga', null, ['read_at' => now()->subDay()]);

        $this->putJson("/api/v1/matches/{$this->match->uuid}/messages/read")->assertOk()->assertJsonPath('data.read', 1);

        $this->assertNull($mine->fresh()->read_at);
        $this->assertNotNull($theirs->fresh()->read_at);
        $this->assertTrue($alreadyRead->fresh()->read_at->lt(now()->subHours(23)));
        $this->putJson("/api/v1/matches/{$this->match->uuid}/messages/read")->assertJsonPath('data.read', 0);
    }

    public function test_match_list_shows_unread_count_and_last_message(): void
    {
        $this->setUpMatch();
        $this->say($this->tutor, 'oi', null, ['created_at' => now()->subMinutes(3)]);
        $this->say($this->other, 'tudo bem?', null, ['created_at' => now()->subMinutes(2)]);
        $this->say($this->other, 'vamos passear?', null, ['created_at' => now()->subMinute()]);

        $item = $this->getJson('/api/v1/matches')->assertOk()->json('data.0');

        $this->assertSame(2, $item['unread_count']);
        $this->assertSame('vamos passear?', $item['last_message']['body']);
        $this->assertFalse($item['last_message']['is_mine']);

        $this->putJson("/api/v1/matches/{$this->match->uuid}/messages/read")->assertOk();
        $this->getJson('/api/v1/matches')->assertJsonPath('data.0.unread_count', 0);
    }

    public function test_match_without_messages_has_null_last_message(): void
    {
        $this->setUpMatch();

        $this->getJson('/api/v1/matches')->assertJsonPath('data.0.unread_count', 0)->assertJsonPath('data.0.last_message', null);
    }

    public function test_sending_is_rate_limited(): void
    {
        $this->setUpMatch();
        $url = "/api/v1/matches/{$this->match->uuid}/messages";

        foreach (range(1, 30) as $i) {
            $this->postJson($url, ['body' => "m{$i}"])->assertCreated();
        }

        $this->postJson($url, ['body' => 'excedeu'])->assertTooManyRequests();
    }
}
