<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ong;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Ong\MyOng\UpdateMyOngLogoRequest;
use App\Http\Requests\Api\Ong\MyOng\UpdateMyOngRequest;
use App\Http\Resources\Ong\MyOngResource;
use App\Models\Ong;
use App\Services\Api\Ong\MyOng\IndexMyOngService;
use App\Services\Api\Ong\MyOng\UpdateMyOngLogoService;
use App\Services\Api\Ong\MyOng\UpdateMyOngService;
use App\Services\Api\Ong\MyOng\ViewMyOngService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MyOngController extends BaseController
{
    public function index(IndexMyOngService $indexMyOngService): AnonymousResourceCollection
    {
        $ongs = $indexMyOngService->run();

        return MyOngResource::collection($ongs);
    }

    public function show(
        Ong $managedOng,
        ViewMyOngService $viewMyOngService
    ): JsonResponse {
        $ong = $viewMyOngService->run($managedOng);

        return $this->successResponse(new MyOngResource($ong), 'ONG encontrada com sucesso.');
    }

    public function update(
        Ong $managedOng,
        UpdateMyOngRequest $updateMyOngRequest,
        UpdateMyOngService $updateMyOngService
    ): JsonResponse {
        $data = $updateMyOngRequest->validated();
        $ong = $updateMyOngService->run($managedOng, $data);

        return $this->successResponse(new MyOngResource($ong), 'ONG atualizada com sucesso.');
    }

    public function logo(
        Ong $managedOng,
        UpdateMyOngLogoRequest $updateMyOngLogoRequest,
        UpdateMyOngLogoService $updateMyOngLogoService
    ): JsonResponse {
        $data = $updateMyOngLogoRequest->validated();
        $ong = $updateMyOngLogoService->run($managedOng, $data);

        return $this->successResponse(new MyOngResource($ong), 'Logo atualizada com sucesso.');
    }
}
