<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngPost;

use App\Models\Ong;
use App\Models\OngPost;
use App\Models\Pet;
use App\Services\BaseService;
use App\Traits\AuthorizesOngActions;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UpdateOngPostService extends BaseService
{
    use AuthorizesOngActions;

    public function run(Ong $managedOng, OngPost $post, array $data): OngPost
    {
        $changesPet = array_key_exists('pet_uuid', $data);
        $petUuid = $data['pet_uuid'] ?? null;

        $this->ensureOngVerified($managedOng);

        $attributes = Arr::except($data, ['pet_uuid']);

        if ($changesPet) {
            $attributes['pet_id'] = $petUuid ? Pet::where('uuid', $petUuid)->value('id') : null;
        }

        return DB::transaction(function () use ($post, $attributes): OngPost {
            $post->update($attributes);

            return $post->refresh()->load(['ong', 'pet']);
        });
    }
}
