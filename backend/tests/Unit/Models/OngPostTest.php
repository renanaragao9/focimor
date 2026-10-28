<?php

namespace Tests\Unit\Models;

use App\Models\Ong;
use App\Models\OngPost;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OngPostTest extends TestCase
{
    use RefreshDatabase;

    protected function makeOngPost(array $attributes = []): OngPost
    {
        return OngPost::create(array_merge(['title' => 'Novo resgate', 'body' => 'Texto', 'ong_id' => Ong::create(['name' => 'ONG', 'slug' => 'ong-'.Str::random(5)])->id], $attributes));
    }

    public function test_published_scope_requires_a_past_date_and_an_unrestricted_ong(): void
    {
        $verified = Ong::create(['name' => 'V', 'slug' => 'v', 'status' => 'verified']);
        $pending = Ong::create(['name' => 'P', 'slug' => 'p', 'status' => 'pending']);
        $visible = $this->makeOngPost(['title' => 'visível', 'published_at' => now()->subDay(), 'ong_id' => $verified->id]);
        $this->makeOngPost(['title' => 'sistema', 'published_at' => now()->subDay(), 'ong_id' => null]);
        $this->makeOngPost(['title' => 'rascunho', 'published_at' => null, 'ong_id' => $verified->id]);
        $this->makeOngPost(['title' => 'futuro', 'published_at' => now()->addDay(), 'ong_id' => $verified->id]);
        $this->makeOngPost(['title' => 'pendente', 'published_at' => now()->subDay(), 'ong_id' => $pending->id]);

        $titles = OngPost::published()->orderBy('title')->pluck('title')->all();

        $this->assertSame(['sistema', 'visível'], $titles);
        $this->assertTrue(OngPost::published()->whereKey($visible->id)->exists());
    }

    public function test_can_create(): void
    {
        $record = $this->makeOngPost();

        $this->assertDatabaseHas('ong_posts', ['id' => $record->id, 'title' => 'Novo resgate']);
    }

    public function test_can_read(): void
    {
        $this->assertNotNull(OngPost::find($this->makeOngPost()->id));
    }

    public function test_can_update(): void
    {
        $record = $this->makeOngPost();

        $record->update(['title' => 'Resgate atualizado']);

        $this->assertDatabaseHas('ong_posts', ['id' => $record->id, 'title' => 'Resgate atualizado']);
    }

    public function test_can_delete(): void
    {
        $record = $this->makeOngPost();

        $record->delete();

        $this->assertSoftDeleted('ong_posts', ['id' => $record->id]);
        $this->assertNull(OngPost::find($record->id));
    }

    public function test_can_be_a_system_post_without_ong(): void
    {
        $post = OngPost::create(['title' => 'Aviso', 'body' => 'Texto']);

        $this->assertNull($post->ong_id);
        $this->assertNull($post->fresh()->ong);
    }

    public function test_published_at_is_cast(): void
    {
        $post = $this->makeOngPost(['published_at' => '2026-01-10 10:00:00']);

        $this->assertInstanceOf(Carbon::class, $post->published_at);
    }

    public function test_relationship(): void
    {
        $this->assertInstanceOf(BelongsTo::class, $this->makeOngPost()->ong());
    }
}
