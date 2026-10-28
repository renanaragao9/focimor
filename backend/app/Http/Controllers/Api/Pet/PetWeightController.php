<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pet;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Pet\PetWeight\IndexPetWeightRequest;
use App\Http\Requests\Api\Pet\PetWeight\StorePetWeightRequest;
use App\Http\Requests\Api\Pet\PetWeight\UpdatePetWeightRequest;
use App\Http\Resources\Pet\PetWeightResource;
use App\Models\Pet;
use App\Models\PetWeight;
use App\Services\Api\Pet\PetWeight\DeletePetWeightService;
use App\Services\Api\Pet\PetWeight\IndexPetWeightService;
use App\Services\Api\Pet\PetWeight\StorePetWeightService;
use App\Services\Api\Pet\PetWeight\UpdatePetWeightService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PetWeightController extends BaseController
{
    public function index(
        Pet $pet,
        IndexPetWeightRequest $indexPetWeightRequest,
        IndexPetWeightService $indexPetWeightService
    ): AnonymousResourceCollection {
        $data = $indexPetWeightRequest->validated();
        $items = $indexPetWeightService->run($pet, $data);

        return PetWeightResource::collection($items);
    }

    public function store(
        Pet $pet,
        StorePetWeightRequest $storePetWeightRequest,
        StorePetWeightService $storePetWeightService
    ): JsonResponse {
        $data = $storePetWeightRequest->validated();
        $weight = $storePetWeightService->run($pet, $data);

        return $this->successResponse(new PetWeightResource($weight), 'Medição registrada com sucesso.', 201);
    }

    public function update(
        Pet $pet,
        PetWeight $weight,
        UpdatePetWeightRequest $updatePetWeightRequest,
        UpdatePetWeightService $updatePetWeightService
    ): JsonResponse {
        $data = $updatePetWeightRequest->validated();
        $weight = $updatePetWeightService->run($weight, $data);

        return $this->successResponse(new PetWeightResource($weight), 'Medição atualizada com sucesso.');
    }

    public function destroy(
        Pet $pet,
        PetWeight $weight,
        DeletePetWeightService $deletePetWeightService
    ): JsonResponse {
        $deletePetWeightService->run($weight);

        return $this->successResponse(null, 'Medição removida com sucesso.');
    }
}
