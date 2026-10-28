<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Adoption;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Adoption\Adoption\DecideAdoptionRequest;
use App\Http\Requests\Api\Adoption\Adoption\IndexAdoptionRequest;
use App\Http\Requests\Api\Adoption\Adoption\IndexReceivedAdoptionRequest;
use App\Http\Resources\Adoption\AdoptionMatchResource;
use App\Models\AdoptionMatch;
use App\Services\Api\Adoption\Adoption\CancelAdoptionService;
use App\Services\Api\Adoption\Adoption\DecideAdoptionService;
use App\Services\Api\Adoption\Adoption\IndexAdoptionService;
use App\Services\Api\Adoption\Adoption\IndexReceivedAdoptionService;
use App\Services\Api\Adoption\Adoption\ViewAdoptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdoptionController extends BaseController
{
    public function index(
        IndexAdoptionRequest $indexAdoptionRequest,
        IndexAdoptionService $indexAdoptionService
    ): AnonymousResourceCollection {
        $data = $indexAdoptionRequest->validated();
        $adoptions = $indexAdoptionService->run($data);

        return AdoptionMatchResource::collection($adoptions);
    }

    public function received(
        IndexReceivedAdoptionRequest $indexReceivedAdoptionRequest,
        IndexReceivedAdoptionService $indexReceivedAdoptionService
    ): AnonymousResourceCollection {
        $data = $indexReceivedAdoptionRequest->validated();
        $adoptions = $indexReceivedAdoptionService->run($data);

        return AdoptionMatchResource::collection($adoptions);
    }

    public function show(
        AdoptionMatch $adoptionMatch,
        ViewAdoptionService $viewAdoptionService
    ): JsonResponse {
        $adoption = $viewAdoptionService->run($adoptionMatch);

        return $this->successResponse(new AdoptionMatchResource($adoption), 'Adoção encontrada com sucesso.');
    }

    public function decide(
        AdoptionMatch $adoptionMatch,
        DecideAdoptionRequest $decideAdoptionRequest,
        DecideAdoptionService $decideAdoptionService
    ): JsonResponse {
        $data = $decideAdoptionRequest->validated();
        $adoption = $decideAdoptionService->run($adoptionMatch, $data);

        return $this->successResponse(new AdoptionMatchResource($adoption), 'Decisão registrada com sucesso.');
    }

    public function cancel(
        AdoptionMatch $adoptionMatch,
        CancelAdoptionService $cancelAdoptionService
    ): JsonResponse {
        $adoption = $cancelAdoptionService->run($adoptionMatch);

        return $this->successResponse(new AdoptionMatchResource($adoption), 'Interesse cancelado com sucesso.');
    }
}
