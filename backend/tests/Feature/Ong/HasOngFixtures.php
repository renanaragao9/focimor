<?php

declare(strict_types=1);

namespace Tests\Feature\Ong;

use App\Models\Ong;
use App\Models\OngPost;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\Feature\Adoption\HasAdoptionFixtures;

trait HasOngFixtures
{
    use HasAdoptionFixtures;

    protected function makeOng(array $overrides = []): Ong
    {
        $name = $overrides['name'] ?? 'ONG '.Str::random(5);

        return Ong::create(array_merge(['name' => $name, 'slug' => Str::slug($name).'-'.Str::random(4), 'status' => 'verified'], $overrides));
    }

    protected function joinOng(Ong $ong, User $user, string $role = 'member'): void
    {
        $ong->users()->attach($user->id, ['role' => $role]);
    }

    protected function makePost(?Ong $ong, array $overrides = []): OngPost
    {
        return OngPost::create(array_merge([
            'title' => 'Post '.Str::random(4),
            'body' => 'Conteúdo',
            'published_at' => now()->subDay(),
            'ong_id' => $ong?->id,
        ], $overrides));
    }
}
