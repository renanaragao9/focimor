<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Global\Select;

use App\Http\Controllers\Api\BaseController;
use App\Services\Api\Global\Select\IndexSpeciesService;
use Illuminate\Http\JsonResponse;

class SpeciesController extends BaseController
{
    public function index(IndexSpeciesService $indexSpeciesService): JsonResponse
    {
        $items = $indexSpeciesService->run();

        return $this->successResponse($items, 'Espécies carregadas com sucesso.');
    }
}
