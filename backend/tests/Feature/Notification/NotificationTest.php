<?php

declare(strict_types=1);

namespace Tests\Feature\Notification;

use App\Models\User;
use App\Notifications\Pet\NewMatchNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Pet\ActsAsTutor;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use ActsAsTutor, RefreshDatabase;

    private function notify(User $user, string $name = 'Rex', int $count = 1): void
    {
        foreach (range(1, $count) as $i) {
            $user->notify(new NewMatchNotification(fake()->uuid(), $name, 'Luna'));
        }
    }

    public function test_requires_authentication(): void
    {
        $this->getJson('/api/v1/notifications')->assertUnauthorized();
        $this->getJson('/api/v1/notifications/unread-count')->assertUnauthorized();
        $this->putJson('/api/v1/notifications/read-all')->assertUnauthorized();
        $this->putJson('/api/v1/notifications/'.fake()->uuid().'/read')->assertUnauthorized();
    }

    public function test_lists_only_my_notifications_newest_first_with_a_clean_shape(): void
    {
        $user = $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $this->notify($user, 'Primeiro');
        $this->travel(1)->minute();
        $this->notify($user, 'Segundo');
        $this->notify($other, 'Alheio');

        $response = $this->getJson('/api/v1/notifications')->assertOk();

        $this->assertSame(['Segundo e Luna deram match.', 'Primeiro e Luna deram match.'], collect($response->json('data'))->pluck('body')->all());
        $item = $response->json('data.0');
        $this->assertSame('new_match', $item['type']);
        $this->assertSame('É um match!', $item['title']);
        $this->assertFalse($item['is_read']);
        $this->assertArrayHasKey('match_uuid', $item['data']);
        $this->assertArrayNotHasKey('notifiable_id', $item);
        $this->assertArrayNotHasKey('type', $item['data']);
        $response->assertJsonPath('meta.total', 2);
    }

    public function test_filters_unread_and_read_and_paginates(): void
    {
        $user = $this->loginAsTutor();
        $this->notify($user, 'A', 3);
        $user->notifications()->first()->markAsRead();

        $this->getJson('/api/v1/notifications?filters[unread]=1')->assertJsonCount(2, 'data');
        $this->getJson('/api/v1/notifications?filters[unread]=0')->assertJsonCount(1, 'data')->assertJsonPath('data.0.is_read', true);
        $this->getJson('/api/v1/notifications?per_page=2')->assertJsonCount(2, 'data')->assertJsonPath('meta.total', 3);
        $this->getJson('/api/v1/notifications?per_page=1000')->assertUnprocessable();
    }

    public function test_unread_count(): void
    {
        $user = $this->loginAsTutor();
        $this->notify($user, 'A', 3);
        $this->notify($this->makeUser('outro@example.com'), 'B', 5);
        $user->notifications()->first()->markAsRead();

        $this->getJson('/api/v1/notifications/unread-count')->assertOk()->assertJsonPath('data.unread', 2);
    }

    public function test_marks_one_as_read(): void
    {
        $user = $this->loginAsTutor();
        $this->notify($user, 'A', 2);
        $target = $user->notifications()->first();

        $this->putJson("/api/v1/notifications/{$target->id}/read")->assertOk()->assertJsonPath('data.is_read', true);

        $this->assertNotNull($target->fresh()->read_at);
        $this->assertSame(1, $user->unreadNotifications()->count());
        $this->putJson("/api/v1/notifications/{$target->id}/read")->assertOk();
    }

    public function test_cannot_read_notifications_of_other_users(): void
    {
        $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $this->notify($other);
        $foreign = $other->notifications()->first();

        $this->putJson("/api/v1/notifications/{$foreign->id}/read")->assertNotFound();
        $this->putJson('/api/v1/notifications/'.fake()->uuid().'/read')->assertNotFound();

        $this->assertNull($foreign->fresh()->read_at);
    }

    public function test_marks_all_as_read_without_touching_other_users(): void
    {
        $user = $this->loginAsTutor();
        $other = $this->makeUser('outro@example.com');
        $this->notify($user, 'A', 3);
        $this->notify($other, 'B', 2);

        $this->putJson('/api/v1/notifications/read-all')->assertOk()->assertJsonPath('data.read', 3);

        $this->assertSame(0, $user->unreadNotifications()->count());
        $this->assertSame(2, $other->unreadNotifications()->count());
        $this->putJson('/api/v1/notifications/read-all')->assertJsonPath('data.read', 0);
    }
}
