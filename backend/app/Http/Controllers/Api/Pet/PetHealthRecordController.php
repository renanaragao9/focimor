<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pet;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Pet\PetHealthRecord\IndexPetHealthRecordRequest;
use App\Http\Requests\Api\Pet\PetHealthRecord\StorePetHealthRecordRequest;
use App\Http\Requests\Api\Pet\PetHealthRecord\UpdatePetHealthRecordRequest;
use App\Http\Resources\Pet\PetHealthRecordResource;
use App\Models\Pet;
use App\Models\PetHealthRecord;
use App\Services\Api\Pet\PetHealthRecord\DeletePetHealthRecordService;
use App\Services\Api\Pet\PetHealthRecord\IndexPetHealthRecordService;
use App\Services\Api\Pet\PetHealthRecord\StorePetHealthRecordService;
use App\Services\Api\Pet\PetHealthRecord\UpdatePetHealthRecordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PetHealthRecordController extends BaseController
{
    public function index(
        Pet $pet,
        IndexPetHealthRecordRequest $indexPetHealthRecordRequest,
        IndexPetHealthRecordService $indexPetHealthRecordService
    ): AnonymousResourceCollection {
        $data = $indexPetHealthRecordRequest->validated();
        $items = $indexPetHealthRecordService->run($pet, $data);

        return PetHealthRecordResource::collection($items);
    }

    public function store(
        Pet $pet,
        StorePetHealthRecordRequest $storePetHealthRecordRequest,
        StorePetHealthRecordService $storePetHealthRecordService
    ): JsonResponse {
        $data = $storePetHealthRecordRequest->validated();
        $healthRecord = $storePetHealthRecordService->run($pet, $data);

        return $this->successResponse(new PetHealthRecordResource($healthRecord), 'Registro de saúde registrado com sucesso.', 201);
    }

    public function update(
        Pet $pet,
        PetHealthRecord $healthRecord,
        UpdatePetHealthRecordRequest $updatePetHealthRecordRequest,
        UpdatePetHealthRecordService $updatePetHealthRecordService
    ): JsonResponse {
        $data = $updatePetHealthRecordRequest->validated();
        $healthRecord = $updatePetHealthRecordService->run($healthRecord, $data);

        return $this->successResponse(new PetHealthRecordResource($healthRecord), 'Registro de saúde atualizado com sucesso.');
    }

    public function destroy(
        Pet $pet,
        PetHealthRecord $healthRecord,
        DeletePetHealthRecordService $deletePetHealthRecordService
    ): JsonResponse {
        $deletePetHealthRecordService->run($healthRecord);

        return $this->successResponse(null, 'Registro de saúde removido com sucesso.');
    }
}
