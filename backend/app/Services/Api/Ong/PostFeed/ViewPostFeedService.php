<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\PostFeed;

use App\Models\OngPost;
use App\Services\BaseService;

class ViewPostFeedService extends BaseService
{
    public function run(OngPost $publicPost): OngPost
    {
        return $publicPost->load(['ong', 'pet']);
    }
}
