<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ong;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Ong\OngPublicPost\IndexOngPublicPostRequest;
use App\Http\Resources\Ong\OngPostResource;
use App\Models\Ong;
use App\Services\Api\Ong\OngPublicPost\IndexOngPublicPostService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OngPublicPostController extends BaseController
{
    public function index(
        Ong $ong,
        IndexOngPublicPostRequest $indexOngPublicPostRequest,
        IndexOngPublicPostService $indexOngPublicPostService
    ): AnonymousResourceCollection {
        $data = $indexOngPublicPostRequest->validated();
        $posts = $indexOngPublicPostService->run($ong, $data);

        return OngPostResource::collection($posts);
    }
}
