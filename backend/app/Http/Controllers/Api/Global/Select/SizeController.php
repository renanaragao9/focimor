<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Global\Select;

use App\Http\Controllers\Api\BaseController;
use App\Services\Api\Global\Select\IndexSizeService;
use Illuminate\Http\JsonResponse;

class SizeController extends BaseController
{
    public function index(IndexSizeService $indexSizeService): JsonResponse
    {
        $items = $indexSizeService->run();

        return $this->successResponse($items, 'Portes carregados com sucesso.');
    }
}
