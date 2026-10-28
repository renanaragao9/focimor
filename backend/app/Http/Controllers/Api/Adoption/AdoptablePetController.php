<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Adoption;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Adoption\AdoptablePet\IndexAdoptablePetRequest;
use App\Http\Resources\PetMatch\PublicPetResource;
use App\Models\Pet;
use App\Services\Api\Adoption\AdoptablePet\IndexAdoptablePetService;
use App\Services\Api\Adoption\AdoptablePet\ViewAdoptablePetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdoptablePetController extends BaseController
{
    public function index(
        IndexAdoptablePetRequest $indexAdoptablePetRequest,
        IndexAdoptablePetService $indexAdoptablePetService
    ): AnonymousResourceCollection {
        $data = $indexAdoptablePetRequest->validated();
        $pets = $indexAdoptablePetService->run($data);

        return PublicPetResource::collection($pets);
    }

    public function show(
        Pet $adoptablePet,
        ViewAdoptablePetService $viewAdoptablePetService
    ): JsonResponse {
        $pet = $viewAdoptablePetService->run($adoptablePet);

        return $this->successResponse(new PublicPetResource($pet), 'Pet encontrado com sucesso.');
    }
}
