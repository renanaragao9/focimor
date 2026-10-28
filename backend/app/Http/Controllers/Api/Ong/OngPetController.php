<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ong;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Ong\OngPet\IndexOngPetRequest;
use App\Http\Requests\Api\Ong\OngPet\StoreOngPetRequest;
use App\Http\Resources\Pet\PetResource;
use App\Models\Ong;
use App\Services\Api\Ong\OngPet\IndexOngPetService;
use App\Services\Api\Ong\OngPet\StoreOngPetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OngPetController extends BaseController
{
    public function index(
        Ong $managedOng,
        IndexOngPetRequest $indexOngPetRequest,
        IndexOngPetService $indexOngPetService
    ): AnonymousResourceCollection {
        $data = $indexOngPetRequest->validated();
        $pets = $indexOngPetService->run($managedOng, $data);

        return PetResource::collection($pets);
    }

    public function store(
        Ong $managedOng,
        StoreOngPetRequest $storeOngPetRequest,
        StoreOngPetService $storeOngPetService
    ): JsonResponse {
        $data = $storeOngPetRequest->validated();
        $pet = $storeOngPetService->run($managedOng, $data);

        return $this->successResponse(new PetResource($pet), 'Pet da ONG criado com sucesso.', 201);
    }
}
