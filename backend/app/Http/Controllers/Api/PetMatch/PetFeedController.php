<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\PetMatch;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\PetMatch\PetFeed\IndexPetFeedRequest;
use App\Http\Resources\PetMatch\PublicPetResource;
use App\Models\Pet;
use App\Services\Api\PetMatch\PetFeed\IndexPetFeedService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PetFeedController extends BaseController
{
    public function index(
        Pet $pet,
        IndexPetFeedRequest $indexPetFeedRequest,
        IndexPetFeedService $indexPetFeedService
    ): AnonymousResourceCollection {
        $data = $indexPetFeedRequest->validated();
        $pets = $indexPetFeedService->run($pet, $data);

        return PublicPetResource::collection($pets);
    }
}
