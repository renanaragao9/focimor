<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngPost;

use App\Models\Ong;
use App\Models\OngPost;
use App\Models\Pet;
use App\Services\BaseService;
use App\Traits\AuthorizesOngActions;
use Illuminate\Support\Facades\DB;

class StoreOngPostService extends BaseService
{
    use AuthorizesOngActions;

    public function run(Ong $managedOng, array $data): OngPost
    {
        $title = $data['title'];
        $body = $data['body'];
        $publishedAt = $data['published_at'] ?? null;
        $petUuid = $data['pet_uuid'] ?? null;
        $cover = $data['cover'] ?? null;

        $this->ensureOngVerified($managedOng);

        $userId = $this->getUserId();

        return DB::transaction(function () use ($managedOng, $title, $body, $publishedAt, $petUuid, $cover, $userId): OngPost {
            $post = $managedOng->posts()->create([
                'title' => $title,
                'body' => $body,
                'published_at' => $publishedAt,
                'pet_id' => $petUuid ? Pet::where('uuid', $petUuid)->value('id') : null,
                'cover_path' => $cover?->store('ong-posts', config('filesystems.media')),
                'author_user_id' => $userId,
            ]);

            return $post->load(['ong', 'pet']);
        });
    }
}
