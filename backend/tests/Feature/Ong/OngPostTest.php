<?php

declare(strict_types=1);

namespace Tests\Feature\Ong;

use App\Models\Ong;
use App\Models\OngPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OngPostTest extends TestCase
{
    use HasOngFixtures, RefreshDatabase;

    private Ong $ong;

    private function setUpAs(string $role = 'member', string $status = 'verified'): void
    {
        Storage::fake(config('filesystems.media'));
        $user = $this->loginAsTutor();
        $this->ong = $this->makeOng(['status' => $status]);
        $this->joinOng($this->ong, $user, $role);
    }

    private function url(string $suffix = ''): string
    {
        return "/api/v1/my-ongs/{$this->ong->uuid}/posts{$suffix}";
    }

    public function test_requires_authentication(): void
    {
        $this->getJson('/api/v1/my-ongs/'.fake()->uuid().'/posts')->assertUnauthorized();
        $this->postJson('/api/v1/my-ongs/'.fake()->uuid().'/posts', [])->assertUnauthorized();
    }

    public function test_any_member_creates_a_published_post_with_author_and_ong(): void
    {
        $this->setUpAs('member');

        $response = $this->postJson($this->url(), ['title' => 'Mutirão', 'body' => 'Neste sábado', 'published_at' => now()->subMinute()->toDateTimeString()])
            ->assertCreated()
            ->assertJsonPath('data.title', 'Mutirão')
            ->assertJsonPath('data.is_published', true)
            ->assertJsonPath('data.ong.uuid', $this->ong->uuid);

        $post = OngPost::where('uuid', $response->json('data.uuid'))->firstOrFail();
        $this->assertSame($this->ong->id, $post->ong_id);
        $this->assertSame($this->tutor->id, $post->author_user_id);
    }

    public function test_post_without_date_is_a_draft_and_not_public(): void
    {
        $this->setUpAs();

        $uuid = $this->postJson($this->url(), ['title' => 'Rascunho', 'body' => 'Em breve'])->assertCreated()->assertJsonPath('data.is_published', false)->json('data.uuid');

        $this->getJson("/api/v1/posts/{$uuid}")->assertNotFound();
        $this->assertSame(0, collect($this->getJson('/api/v1/posts')->json('data'))->count());

        $this->putJson($this->url("/{$uuid}"), ['published_at' => now()->subMinute()->toDateTimeString()])->assertOk()->assertJsonPath('data.is_published', true);
        $this->getJson("/api/v1/posts/{$uuid}")->assertOk();
    }

    public function test_cannot_publish_when_the_ong_is_not_verified(): void
    {
        $this->setUpAs('admin', 'pending');
        $post = $this->makePost($this->ong, ['published_at' => null]);

        $this->postJson($this->url(), ['title' => 'X', 'body' => 'Y'])->assertUnprocessable()->assertJsonPath('message', 'A ONG precisa estar verificada para publicar.');
        $this->putJson($this->url("/{$post->uuid}"), ['title' => 'Novo'])->assertUnprocessable();
        $this->post($this->url("/{$post->uuid}/cover"), ['cover' => UploadedFile::fake()->image('a.jpg')], ['Accept' => 'application/json'])->assertUnprocessable();
        $this->assertSame(1, OngPost::count());
    }

    public function test_validates_post_data(): void
    {
        $this->setUpAs();

        $this->postJson($this->url(), [])->assertUnprocessable()->assertJsonValidationErrors(['title', 'body'], 'errors');
        $this->postJson($this->url(), ['title' => str_repeat('a', 256), 'body' => 'x'])->assertUnprocessable();
        $this->postJson($this->url(), ['title' => 'x', 'body' => 'y', 'published_at' => 'não é data'])->assertUnprocessable();
    }

    public function test_pet_must_belong_to_the_same_ong(): void
    {
        $this->setUpAs();
        $owner = $this->makeUser('dono@example.com');
        $mine = $this->makeAdoptionPetFor($owner, ['name' => 'Bidu', 'ong_id' => $this->ong->id]);
        $foreign = $this->makeAdoptionPetFor($owner, ['name' => 'Alheio', 'ong_id' => $this->makeOng()->id]);
        $personal = $this->makeAdoptionPetFor($owner, ['name' => 'Particular']);

        $this->postJson($this->url(), ['title' => 'A', 'body' => 'B', 'pet_uuid' => $mine->uuid])->assertCreated()->assertJsonPath('data.pet.name', 'Bidu');
        $this->postJson($this->url(), ['title' => 'A', 'body' => 'B', 'pet_uuid' => $foreign->uuid])->assertUnprocessable()->assertJsonPath('message', 'O pet informado não pertence a esta ONG.');
        $this->postJson($this->url(), ['title' => 'A', 'body' => 'B', 'pet_uuid' => $personal->uuid])->assertUnprocessable();
    }

    public function test_store_with_cover_and_replace_cover_removes_the_old_file(): void
    {
        $this->setUpAs();

        $uuid = $this->post($this->url(), ['title' => 'Com capa', 'body' => 'Texto', 'cover' => UploadedFile::fake()->image('a.jpg')], ['Accept' => 'application/json'])
            ->assertCreated()->json('data.uuid');
        $post = OngPost::where('uuid', $uuid)->firstOrFail();
        $first = $post->cover_path;
        Storage::disk(config('filesystems.media'))->assertExists($first);

        $this->post($this->url("/{$uuid}/cover"), ['cover' => UploadedFile::fake()->image('b.jpg')], ['Accept' => 'application/json'])->assertOk();

        Storage::disk(config('filesystems.media'))->assertMissing($first);
        Storage::disk(config('filesystems.media'))->assertExists($post->fresh()->cover_path);
        $this->post($this->url("/{$uuid}/cover"), ['cover' => UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')], ['Accept' => 'application/json'])->assertUnprocessable();
    }

    public function test_shows_a_draft_post_of_the_ong_but_not_of_another(): void
    {
        $this->setUpAs('member');
        $draft = OngPost::create(['ong_id' => $this->ong->id, 'title' => 'Rascunho', 'body' => 'Texto']);
        $foreign = OngPost::create(['ong_id' => $this->makeOng()->id, 'title' => 'Outra', 'body' => 'Texto']);

        $this->getJson($this->url("/{$draft->uuid}"))->assertOk()->assertJsonPath('data.title', 'Rascunho')->assertJsonPath('data.is_published', false);
        $this->getJson($this->url("/{$foreign->uuid}"))->assertNotFound();
    }

    public function test_lists_all_posts_of_the_ong_including_drafts(): void
    {
        $this->setUpAs();
        $this->makePost($this->ong, ['title' => 'Publicado']);
        $this->makePost($this->ong, ['title' => 'Rascunho', 'published_at' => null]);
        $this->makePost($this->makeOng(), ['title' => 'De outra']);

        $titles = fn ($r) => collect($r->json('data'))->pluck('title')->all();

        $this->assertEqualsCanonicalizing(['Publicado', 'Rascunho'], $titles($this->getJson($this->url())->assertOk()));
        $this->assertSame(['Rascunho'], $titles($this->getJson($this->url('?filters[published]=0'))));
        $this->assertSame(['Publicado'], $titles($this->getJson($this->url('?filters[published]=1'))));
        $this->assertSame(['Publicado'], $titles($this->getJson($this->url('?search=publi'))));
    }

    public function test_update_changes_fields_and_can_clear_the_pet(): void
    {
        $this->setUpAs();
        $pet = $this->makeAdoptionPetFor($this->makeUser('dono@example.com'), ['ong_id' => $this->ong->id]);
        $post = $this->makePost($this->ong, ['title' => 'Antigo', 'pet_id' => $pet->id]);

        $this->putJson($this->url("/{$post->uuid}"), ['title' => 'Novo título', 'pet_uuid' => null])
            ->assertOk()
            ->assertJsonPath('data.title', 'Novo título')
            ->assertJsonPath('data.pet', null);

        $this->assertNull($post->fresh()->pet_id);
        $this->assertSame('Conteúdo', $post->fresh()->body);
    }

    public function test_destroy_soft_deletes_and_hides_the_post_publicly(): void
    {
        $this->setUpAs();
        $post = $this->makePost($this->ong);

        $this->deleteJson($this->url("/{$post->uuid}"))->assertOk();

        $this->assertSoftDeleted('ong_posts', ['id' => $post->id]);
        $this->getJson("/api/v1/posts/{$post->uuid}")->assertNotFound();
    }

    public function test_cannot_touch_posts_of_other_ongs_or_as_a_non_member(): void
    {
        $this->setUpAs();
        $foreignOng = $this->makeOng();
        $foreign = $this->makePost($foreignOng, ['title' => 'Alheio']);

        $this->putJson($this->url("/{$foreign->uuid}"), ['title' => 'Hack'])->assertNotFound();
        $this->deleteJson($this->url("/{$foreign->uuid}"))->assertNotFound();
        $this->getJson("/api/v1/my-ongs/{$foreignOng->uuid}/posts")->assertNotFound();
        $this->postJson("/api/v1/my-ongs/{$foreignOng->uuid}/posts", ['title' => 'x', 'body' => 'y'])->assertNotFound();

        $this->assertSame('Alheio', $foreign->fresh()->title);
    }
}
