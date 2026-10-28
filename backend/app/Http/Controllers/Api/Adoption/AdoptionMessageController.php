<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Adoption;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Adoption\AdoptionMessage\IndexAdoptionMessageRequest;
use App\Http\Requests\Api\Adoption\AdoptionMessage\StoreAdoptionMessageRequest;
use App\Http\Resources\PetMatch\PetMessageResource;
use App\Models\AdoptionMatch;
use App\Services\Api\Adoption\AdoptionMessage\IndexAdoptionMessageService;
use App\Services\Api\Adoption\AdoptionMessage\ReadAdoptionMessageService;
use App\Services\Api\Adoption\AdoptionMessage\StoreAdoptionMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdoptionMessageController extends BaseController
{
    public function index(
        AdoptionMatch $adoptionMatch,
        IndexAdoptionMessageRequest $indexAdoptionMessageRequest,
        IndexAdoptionMessageService $indexAdoptionMessageService
    ): AnonymousResourceCollection {
        $data = $indexAdoptionMessageRequest->validated();
        $messages = $indexAdoptionMessageService->run($adoptionMatch, $data);

        return PetMessageResource::collection($messages);
    }

    public function store(
        AdoptionMatch $adoptionMatch,
        StoreAdoptionMessageRequest $storeAdoptionMessageRequest,
        StoreAdoptionMessageService $storeAdoptionMessageService
    ): JsonResponse {
        $data = $storeAdoptionMessageRequest->validated();
        $message = $storeAdoptionMessageService->run($adoptionMatch, $data);

        return $this->successResponse(new PetMessageResource($message), 'Mensagem enviada com sucesso.', 201);
    }

    public function read(
        AdoptionMatch $adoptionMatch,
        ReadAdoptionMessageService $readAdoptionMessageService
    ): JsonResponse {
        $count = $readAdoptionMessageService->run($adoptionMatch);

        return $this->successResponse(['read' => $count], 'Mensagens marcadas como lidas.');
    }
}
