<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pet;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Pet\PetVaccine\IndexPetVaccineRequest;
use App\Http\Requests\Api\Pet\PetVaccine\StorePetVaccineRequest;
use App\Http\Requests\Api\Pet\PetVaccine\UpdatePetVaccineRequest;
use App\Http\Resources\Pet\PetVaccineResource;
use App\Models\Pet;
use App\Models\PetVaccine;
use App\Services\Api\Pet\PetVaccine\DeletePetVaccineService;
use App\Services\Api\Pet\PetVaccine\IndexPetVaccineService;
use App\Services\Api\Pet\PetVaccine\StorePetVaccineService;
use App\Services\Api\Pet\PetVaccine\UpdatePetVaccineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PetVaccineController extends BaseController
{
    public function index(
        Pet $pet,
        IndexPetVaccineRequest $indexPetVaccineRequest,
        IndexPetVaccineService $indexPetVaccineService
    ): AnonymousResourceCollection {
        $data = $indexPetVaccineRequest->validated();
        $items = $indexPetVaccineService->run($pet, $data);

        return PetVaccineResource::collection($items);
    }

    public function store(
        Pet $pet,
        StorePetVaccineRequest $storePetVaccineRequest,
        StorePetVaccineService $storePetVaccineService
    ): JsonResponse {
        $data = $storePetVaccineRequest->validated();
        $vaccine = $storePetVaccineService->run($pet, $data);

        return $this->successResponse(new PetVaccineResource($vaccine), 'Vacina registrada com sucesso.', 201);
    }

    public function update(
        Pet $pet,
        PetVaccine $vaccine,
        UpdatePetVaccineRequest $updatePetVaccineRequest,
        UpdatePetVaccineService $updatePetVaccineService
    ): JsonResponse {
        $data = $updatePetVaccineRequest->validated();
        $vaccine = $updatePetVaccineService->run($vaccine, $data);

        return $this->successResponse(new PetVaccineResource($vaccine), 'Vacina atualizada com sucesso.');
    }

    public function destroy(
        Pet $pet,
        PetVaccine $vaccine,
        DeletePetVaccineService $deletePetVaccineService
    ): JsonResponse {
        $deletePetVaccineService->run($vaccine);

        return $this->successResponse(null, 'Vacina removida com sucesso.');
    }
}
