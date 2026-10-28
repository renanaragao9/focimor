<?php

declare(strict_types=1);

namespace Tests\Feature\Ong;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostFeedTest extends TestCase
{
    use HasOngFixtures, RefreshDatabase;

    private function titles($response): array
    {
        return collect($response->json('data'))->pluck('title')->all();
    }

    public function test_is_public(): void
    {
        $post = $this->makePost($this->makeOng());

        $this->getJson('/api/v1/posts')->assertOk();
        $this->getJson("/api/v1/posts/{$post->uuid}")->assertOk();
        $this->getJson('/api/v1/posts/'.fake()->uuid())->assertNotFound();
    }

    public function test_feed_mixes_system_and_verified_ong_posts_newest_first(): void
    {
        $this->loginAsTutor();
        $ong = $this->makeOng(['name' => 'Patas']);
        $this->makePost($ong, ['title' => 'Da ONG', 'published_at' => now()->subDays(2)]);
        $this->makePost(null, ['title' => 'Do sistema', 'published_at' => now()->subDay()]);

        $response = $this->getJson('/api/v1/posts')->assertOk();

        $this->assertSame(['Do sistema', 'Da ONG'], $this->titles($response));
        $response->assertJsonPath('data.0.ong', null)->assertJsonPath('data.1.ong.name', 'Patas');
    }

    public function test_hides_drafts_scheduled_and_posts_of_unverified_ongs(): void
    {
        $this->loginAsTutor();
        $ong = $this->makeOng();
        $this->makePost($ong, ['title' => 'Rascunho', 'published_at' => null]);
        $this->makePost($ong, ['title' => 'Agendado', 'published_at' => now()->addHour()]);
        $this->makePost($this->makeOng(['status' => 'pending']), ['title' => 'Pendente']);
        $this->makePost($this->makeOng(['status' => 'suspended']), ['title' => 'Suspensa']);
        $this->makePost(null, ['title' => 'Sistema rascunho', 'published_at' => null]);
        $this->makePost($ong, ['title' => 'Visível']);

        $this->assertSame(['Visível'], $this->titles($this->getJson('/api/v1/posts')));
    }

    public function test_filters_by_ong_system_and_search(): void
    {
        $this->loginAsTutor();
        $ong = $this->makeOng();
        $this->makePost($ong, ['title' => 'Campanha de ração']);
        $this->makePost($this->makeOng(), ['title' => 'Outra']);
        $this->makePost(null, ['title' => 'Aviso do sistema']);

        $this->assertSame(['Campanha de ração'], $this->titles($this->getJson("/api/v1/posts?filters[ong_uuid]={$ong->uuid}")));
        $this->assertSame(['Aviso do sistema'], $this->titles($this->getJson('/api/v1/posts?filters[system]=1')));
        $this->assertSame(['Campanha de ração'], $this->titles($this->getJson('/api/v1/posts?search=ração')));
        $this->getJson('/api/v1/posts?filters[ong_uuid]=x')->assertUnprocessable();
    }

    public function test_paginates_and_sorts(): void
    {
        $this->loginAsTutor();
        foreach (['A', 'B', 'C'] as $i => $title) {
            $this->makePost(null, ['title' => $title, 'published_at' => now()->subDays(3 - $i)]);
        }

        $this->assertSame(['C', 'B'], $this->titles($this->getJson('/api/v1/posts?per_page=2')));
        $this->assertSame(['A', 'B', 'C'], $this->titles($this->getJson('/api/v1/posts?sort=title&direction=asc')));
        $this->getJson('/api/v1/posts?per_page=2')->assertJsonPath('meta.total', 3);
    }

    public function test_show_returns_published_post_with_pet_and_hides_the_rest(): void
    {
        $this->loginAsTutor();
        $ong = $this->makeOng();
        $pet = $this->makeAdoptionPetFor($this->makeUser('dono@example.com'), ['name' => 'Bidu', 'ong_id' => $ong->id]);
        $post = $this->makePost($ong, ['title' => 'Bidu procura família', 'pet_id' => $pet->id]);
        $draft = $this->makePost($ong, ['published_at' => null]);
        $pending = $this->makePost($this->makeOng(['status' => 'pending']));

        $this->getJson("/api/v1/posts/{$post->uuid}")
            ->assertOk()
            ->assertJsonPath('data.title', 'Bidu procura família')
            ->assertJsonPath('data.pet.name', 'Bidu')
            ->assertJsonPath('data.is_published', true)
            ->assertJsonMissingPath('data.id');
        $this->getJson("/api/v1/posts/{$draft->uuid}")->assertNotFound();
        $this->getJson("/api/v1/posts/{$pending->uuid}")->assertNotFound();
    }
}
