<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngPost;

use App\Models\OngPost;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class DeleteOngPostService extends BaseService
{
    public function run(OngPost $post): void
    {
        DB::transaction(fn () => $post->delete());
    }
}
