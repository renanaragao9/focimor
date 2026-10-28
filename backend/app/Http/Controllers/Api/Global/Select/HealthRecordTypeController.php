<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Global\Select;

use App\Http\Controllers\Api\BaseController;
use App\Services\Api\Global\Select\IndexHealthRecordTypeService;
use Illuminate\Http\JsonResponse;

class HealthRecordTypeController extends BaseController
{
    public function index(IndexHealthRecordTypeService $indexHealthRecordTypeService): JsonResponse
    {
        $items = $indexHealthRecordTypeService->run();

        return $this->successResponse($items, 'Tipos de registro de saúde carregados com sucesso.');
    }
}
