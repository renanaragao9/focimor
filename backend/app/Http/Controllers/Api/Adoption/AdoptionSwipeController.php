<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Adoption;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Adoption\AdoptionSwipe\StoreAdoptionSwipeRequest;
use App\Http\Resources\Adoption\AdoptionMatchResource;
use App\Services\Api\Adoption\AdoptionSwipe\StoreAdoptionSwipeService;
use Illuminate\Http\JsonResponse;

class AdoptionSwipeController extends BaseController
{
    public function store(
        StoreAdoptionSwipeRequest $storeAdoptionSwipeRequest,
        StoreAdoptionSwipeService $storeAdoptionSwipeService
    ): JsonResponse {
        $data = $storeAdoptionSwipeRequest->validated();
        $result = $storeAdoptionSwipeService->run($data);

        $swipe = $result['swipe'];
        $pet = $result['pet'];
        $adoption = $result['adoption'];

        return $this->successResponse([
            'swipe' => ['action' => $swipe->action, 'pet_uuid' => $pet->uuid],
            'interested' => $adoption !== null,
            'adoption' => $adoption ? new AdoptionMatchResource($adoption) : null,
        ], $adoption ? 'Interesse enviado ao dono do pet.' : 'Swipe registrado com sucesso.', 201);
    }
}
