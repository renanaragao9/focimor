<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ong;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Ong\OngPost\IndexOngPostRequest;
use App\Http\Requests\Api\Ong\OngPost\StoreOngPostRequest;
use App\Http\Requests\Api\Ong\OngPost\UpdateOngPostCoverRequest;
use App\Http\Requests\Api\Ong\OngPost\UpdateOngPostRequest;
use App\Http\Resources\Ong\OngPostResource;
use App\Models\Ong;
use App\Models\OngPost;
use App\Services\Api\Ong\OngPost\DeleteOngPostService;
use App\Services\Api\Ong\OngPost\IndexOngPostService;
use App\Services\Api\Ong\OngPost\StoreOngPostService;
use App\Services\Api\Ong\OngPost\UpdateOngPostCoverService;
use App\Services\Api\Ong\OngPost\UpdateOngPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OngPostController extends BaseController
{
    public function index(
        Ong $managedOng,
        IndexOngPostRequest $indexOngPostRequest,
        IndexOngPostService $indexOngPostService
    ): AnonymousResourceCollection {
        $data = $indexOngPostRequest->validated();
        $posts = $indexOngPostService->run($managedOng, $data);

        return OngPostResource::collection($posts);
    }

    public function store(
        Ong $managedOng,
        StoreOngPostRequest $storeOngPostRequest,
        StoreOngPostService $storeOngPostService
    ): JsonResponse {
        $data = $storeOngPostRequest->validated();
        $post = $storeOngPostService->run($managedOng, $data);

        return $this->successResponse(new OngPostResource($post), 'Post criado com sucesso.', 201);
    }

    public function show(Ong $managedOng, OngPost $post): JsonResponse
    {
        return $this->successResponse(new OngPostResource($post->load(['ong', 'pet'])), 'Post encontrado com sucesso.');
    }

    public function update(
        Ong $managedOng,
        OngPost $post,
        UpdateOngPostRequest $updateOngPostRequest,
        UpdateOngPostService $updateOngPostService
    ): JsonResponse {
        $data = $updateOngPostRequest->validated();
        $post = $updateOngPostService->run($managedOng, $post, $data);

        return $this->successResponse(new OngPostResource($post), 'Post atualizado com sucesso.');
    }

    public function cover(
        Ong $managedOng,
        OngPost $post,
        UpdateOngPostCoverRequest $updateOngPostCoverRequest,
        UpdateOngPostCoverService $updateOngPostCoverService
    ): JsonResponse {
        $data = $updateOngPostCoverRequest->validated();
        $post = $updateOngPostCoverService->run($managedOng, $post, $data);

        return $this->successResponse(new OngPostResource($post), 'Capa atualizada com sucesso.');
    }

    public function destroy(
        Ong $managedOng,
        OngPost $post,
        DeleteOngPostService $deleteOngPostService
    ): JsonResponse {
        $deleteOngPostService->run($post);

        return $this->successResponse(null, 'Post removido com sucesso.');
    }
}
