<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pet;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Pet\Pet\IndexPetRequest;
use App\Http\Requests\Api\Pet\Pet\StorePetRequest;
use App\Http\Requests\Api\Pet\Pet\UpdatePetRequest;
use App\Http\Resources\Pet\PetResource;
use App\Models\Pet;
use App\Services\Api\Pet\Pet\DeletePetService;
use App\Services\Api\Pet\Pet\IndexPetService;
use App\Services\Api\Pet\Pet\StorePetService;
use App\Services\Api\Pet\Pet\UpdatePetService;
use App\Services\Api\Pet\Pet\ViewPetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PetController extends BaseController
{
    public function index(
        IndexPetRequest $indexPetRequest,
        IndexPetService $indexPetService
    ): AnonymousResourceCollection {
        $data = $indexPetRequest->validated();
        $pets = $indexPetService->run($data);

        return PetResource::collection($pets);
    }

    public function show(
        Pet $pet,
        ViewPetService $viewPetService
    ): JsonResponse {
        $pet = $viewPetService->run($pet);

        return $this->successResponse(new PetResource($pet), 'Pet encontrado com sucesso.');
    }

    public function store(
        StorePetRequest $storePetRequest,
        StorePetService $storePetService
    ): JsonResponse {
        $data = $storePetRequest->validated();
        $pet = $storePetService->run($data);

        return $this->successResponse(new PetResource($pet), 'Pet criado com sucesso.', 201);
    }

    public function update(
        Pet $pet,
        UpdatePetRequest $updatePetRequest,
        UpdatePetService $updatePetService
    ): JsonResponse {
        $data = $updatePetRequest->validated();
        $pet = $updatePetService->run($pet, $data);

        return $this->successResponse(new PetResource($pet), 'Pet atualizado com sucesso.');
    }

    public function destroy(
        Pet $pet,
        DeletePetService $deletePetService
    ): JsonResponse {
        $deletePetService->run($pet);

        return $this->successResponse(null, 'Pet removido com sucesso.');
    }
}
