<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pet;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Pet\PetDetail\UpdatePetDetailRequest;
use App\Http\Resources\Pet\PetDetailResource;
use App\Models\Pet;
use App\Services\Api\Pet\PetDetail\UpdatePetDetailService;
use App\Services\Api\Pet\PetDetail\ViewPetDetailService;
use Illuminate\Http\JsonResponse;

class PetDetailController extends BaseController
{
    public function show(
        Pet $pet,
        ViewPetDetailService $viewPetDetailService
    ): JsonResponse {
        $detail = $viewPetDetailService->run($pet);

        return $this->successResponse($detail ? new PetDetailResource($detail) : null, 'Detalhes do pet carregados com sucesso.');
    }

    public function update(
        Pet $pet,
        UpdatePetDetailRequest $updatePetDetailRequest,
        UpdatePetDetailService $updatePetDetailService
    ): JsonResponse {
        $data = $updatePetDetailRequest->validated();
        $detail = $updatePetDetailService->run($pet, $data);

        return $this->successResponse(new PetDetailResource($detail), 'Detalhes do pet salvos com sucesso.');
    }
}
