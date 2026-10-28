<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pet;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Pet\PetPhoto\StorePetPhotoRequest;
use App\Http\Requests\Api\Pet\PetPhoto\UpdatePetPhotoRequest;
use App\Http\Resources\Pet\PetPhotoResource;
use App\Models\Pet;
use App\Models\PetPhoto;
use App\Services\Api\Pet\PetPhoto\DeletePetPhotoService;
use App\Services\Api\Pet\PetPhoto\IndexPetPhotoService;
use App\Services\Api\Pet\PetPhoto\StorePetPhotoService;
use App\Services\Api\Pet\PetPhoto\UpdatePetPhotoService;
use Illuminate\Http\JsonResponse;

class PetPhotoController extends BaseController
{
    public function index(
        Pet $pet,
        IndexPetPhotoService $indexPetPhotoService
    ): JsonResponse {
        $photos = $indexPetPhotoService->run($pet);

        return $this->successResponse(PetPhotoResource::collection($photos), 'Fotos carregadas com sucesso.');
    }

    public function store(
        Pet $pet,
        StorePetPhotoRequest $storePetPhotoRequest,
        StorePetPhotoService $storePetPhotoService
    ): JsonResponse {
        $data = $storePetPhotoRequest->validated();
        $photo = $storePetPhotoService->run($pet, $data);

        return $this->successResponse(new PetPhotoResource($photo), 'Foto adicionada com sucesso.', 201);
    }

    public function update(
        Pet $pet,
        PetPhoto $photo,
        UpdatePetPhotoRequest $updatePetPhotoRequest,
        UpdatePetPhotoService $updatePetPhotoService
    ): JsonResponse {
        $data = $updatePetPhotoRequest->validated();
        $photo = $updatePetPhotoService->run($photo, $data);

        return $this->successResponse(new PetPhotoResource($photo), 'Foto atualizada com sucesso.');
    }

    public function destroy(
        Pet $pet,
        PetPhoto $photo,
        DeletePetPhotoService $deletePetPhotoService
    ): JsonResponse {
        $deletePetPhotoService->run($photo);

        return $this->successResponse(null, 'Foto removida com sucesso.');
    }
}
