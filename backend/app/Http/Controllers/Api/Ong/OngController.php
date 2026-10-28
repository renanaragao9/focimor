<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ong;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Ong\Ong\IndexOngRequest;
use App\Http\Resources\Ong\PublicOngResource;
use App\Models\Ong;
use App\Services\Api\Ong\Ong\IndexOngService;
use App\Services\Api\Ong\Ong\ViewOngService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OngController extends BaseController
{
    public function index(
        IndexOngRequest $indexOngRequest,
        IndexOngService $indexOngService
    ): AnonymousResourceCollection {
        $data = $indexOngRequest->validated();
        $ongs = $indexOngService->run($data);

        return PublicOngResource::collection($ongs);
    }

    public function show(
        Ong $ong,
        ViewOngService $viewOngService
    ): JsonResponse {
        $ong = $viewOngService->run($ong);

        return $this->successResponse(new PublicOngResource($ong), 'ONG encontrada com sucesso.');
    }
}
