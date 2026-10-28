<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\PetMatch;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\PetMatch\PetSwipe\StorePetSwipeRequest;
use App\Http\Resources\PetMatch\PetMatchResource;
use App\Models\Pet;
use App\Services\Api\PetMatch\PetSwipe\StorePetSwipeService;
use Illuminate\Http\JsonResponse;

class PetSwipeController extends BaseController
{
    public function store(
        Pet $pet,
        StorePetSwipeRequest $storePetSwipeRequest,
        StorePetSwipeService $storePetSwipeService
    ): JsonResponse {
        $data = $storePetSwipeRequest->validated();
        $result = $storePetSwipeService->run($pet, $data);

        $swipe = $result['swipe'];
        $target = $result['target'];
        $match = $result['match'];

        return $this->successResponse([
            'swipe' => ['action' => $swipe->action, 'target_pet_uuid' => $target->uuid],
            'matched' => $match !== null,
            'match' => $match ? new PetMatchResource($match) : null,
        ], $match ? 'É um match!' : 'Swipe registrado com sucesso.', 201);
    }
}
