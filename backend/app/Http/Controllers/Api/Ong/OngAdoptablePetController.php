<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ong;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Ong\OngAdoptablePet\IndexOngAdoptablePetRequest;
use App\Http\Resources\PetMatch\PublicPetResource;
use App\Models\Ong;
use App\Services\Api\Ong\OngAdoptablePet\IndexOngAdoptablePetService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OngAdoptablePetController extends BaseController
{
    public function index(
        Ong $ong,
        IndexOngAdoptablePetRequest $indexOngAdoptablePetRequest,
        IndexOngAdoptablePetService $indexOngAdoptablePetService
    ): AnonymousResourceCollection {
        $data = $indexOngAdoptablePetRequest->validated();
        $pets = $indexOngAdoptablePetService->run($ong, $data);

        return PublicPetResource::collection($pets);
    }
}
