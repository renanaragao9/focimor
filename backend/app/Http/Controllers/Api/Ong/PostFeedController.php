<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ong;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Ong\PostFeed\IndexPostFeedRequest;
use App\Http\Resources\Ong\OngPostResource;
use App\Models\OngPost;
use App\Services\Api\Ong\PostFeed\IndexPostFeedService;
use App\Services\Api\Ong\PostFeed\ViewPostFeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostFeedController extends BaseController
{
    public function index(
        IndexPostFeedRequest $indexPostFeedRequest,
        IndexPostFeedService $indexPostFeedService
    ): AnonymousResourceCollection {
        $data = $indexPostFeedRequest->validated();
        $posts = $indexPostFeedService->run($data);

        return OngPostResource::collection($posts);
    }

    public function show(
        OngPost $publicPost,
        ViewPostFeedService $viewPostFeedService
    ): JsonResponse {
        $post = $viewPostFeedService->run($publicPost);

        return $this->successResponse(new OngPostResource($post), 'Post encontrado com sucesso.');
    }
}
