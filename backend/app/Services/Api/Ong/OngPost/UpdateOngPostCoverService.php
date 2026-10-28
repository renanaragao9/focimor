<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngPost;

use App\Models\Ong;
use App\Models\OngPost;
use App\Services\BaseService;
use App\Traits\AuthorizesOngActions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateOngPostCoverService extends BaseService
{
    use AuthorizesOngActions;

    public function run(Ong $managedOng, OngPost $post, array $data): OngPost
    {
        $cover = $data['cover'];

        $this->ensureOngVerified($managedOng);

        return DB::transaction(function () use ($post, $cover): OngPost {
            $previous = $post->cover_path;

            $post->update(['cover_path' => $cover->store('ong-posts', config('filesystems.media'))]);

            if ($previous) {
                Storage::disk(config('filesystems.media'))->delete($previous);
            }

            return $post->refresh()->load(['ong', 'pet']);
        });
    }
}
