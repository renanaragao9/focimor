<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\PetMatch;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\PetMatch\PetMessage\IndexPetMessageRequest;
use App\Http\Requests\Api\PetMatch\PetMessage\StorePetMessageRequest;
use App\Http\Resources\PetMatch\PetMessageResource;
use App\Models\PetMatch;
use App\Services\Api\PetMatch\PetMessage\IndexPetMessageService;
use App\Services\Api\PetMatch\PetMessage\ReadPetMessageService;
use App\Services\Api\PetMatch\PetMessage\StorePetMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PetMessageController extends BaseController
{
    public function index(
        PetMatch $petMatch,
        IndexPetMessageRequest $indexPetMessageRequest,
        IndexPetMessageService $indexPetMessageService
    ): AnonymousResourceCollection {
        $data = $indexPetMessageRequest->validated();
        $messages = $indexPetMessageService->run($petMatch, $data);

        return PetMessageResource::collection($messages);
    }

    public function store(
        PetMatch $petMatch,
        StorePetMessageRequest $storePetMessageRequest,
        StorePetMessageService $storePetMessageService
    ): JsonResponse {
        $data = $storePetMessageRequest->validated();
        $message = $storePetMessageService->run($petMatch, $data);

        return $this->successResponse(new PetMessageResource($message), 'Mensagem enviada com sucesso.', 201);
    }

    public function read(
        PetMatch $petMatch,
        ReadPetMessageService $readPetMessageService
    ): JsonResponse {
        $count = $readPetMessageService->run($petMatch);

        return $this->successResponse(['read' => $count], 'Mensagens marcadas como lidas.');
    }
}
