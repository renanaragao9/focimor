<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\PetMatch;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\PetMatch\PetMatch\IndexPetMatchRequest;
use App\Http\Resources\PetMatch\PetMatchResource;
use App\Models\PetMatch;
use App\Services\Api\PetMatch\PetMatch\DeletePetMatchService;
use App\Services\Api\PetMatch\PetMatch\IndexPetMatchService;
use App\Services\Api\PetMatch\PetMatch\ViewPetMatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PetMatchController extends BaseController
{
    public function index(
        IndexPetMatchRequest $indexPetMatchRequest,
        IndexPetMatchService $indexPetMatchService
    ): AnonymousResourceCollection {
        $data = $indexPetMatchRequest->validated();
        $matches = $indexPetMatchService->run($data);

        return PetMatchResource::collection($matches);
    }

    public function show(
        PetMatch $petMatch,
        ViewPetMatchService $viewPetMatchService
    ): JsonResponse {
        $match = $viewPetMatchService->run($petMatch);

        return $this->successResponse(new PetMatchResource($match), 'Match encontrado com sucesso.');
    }

    public function destroy(
        PetMatch $petMatch,
        DeletePetMatchService $deletePetMatchService
    ): JsonResponse {
        $deletePetMatchService->run($petMatch);

        return $this->successResponse(null, 'Match desfeito com sucesso.');
    }
}
