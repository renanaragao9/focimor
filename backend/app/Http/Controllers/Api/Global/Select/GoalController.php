<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Global\Select;

use App\Http\Controllers\Api\BaseController;
use App\Services\Api\Global\Select\IndexGoalService;
use Illuminate\Http\JsonResponse;

class GoalController extends BaseController
{
    public function index(IndexGoalService $indexGoalService): JsonResponse
    {
        $items = $indexGoalService->run();

        return $this->successResponse($items, 'Objetivos carregados com sucesso.');
    }
}
