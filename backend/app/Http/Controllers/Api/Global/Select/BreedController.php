<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Global\Select;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Global\Select\IndexBreedRequest;
use App\Services\Api\Global\Select\IndexBreedService;
use Illuminate\Http\JsonResponse;

class BreedController extends BaseController
{
    public function index(
        IndexBreedRequest $indexBreedRequest,
        IndexBreedService $indexBreedService
    ): JsonResponse {
        $data = $indexBreedRequest->validated();
        $breeds = $indexBreedService->run($data);

        return $this->successResponse($breeds, 'Raças carregadas com sucesso.');
    }
}
