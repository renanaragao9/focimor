<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Adoption;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Adoption\AdoptionFeed\IndexAdoptionFeedRequest;
use App\Http\Resources\PetMatch\PublicPetResource;
use App\Services\Api\Adoption\AdoptionFeed\IndexAdoptionFeedService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdoptionFeedController extends BaseController
{
    public function index(
        IndexAdoptionFeedRequest $indexAdoptionFeedRequest,
        IndexAdoptionFeedService $indexAdoptionFeedService
    ): AnonymousResourceCollection {
        $data = $indexAdoptionFeedRequest->validated();
        $pets = $indexAdoptionFeedService->run($data);

        return PublicPetResource::collection($pets);
    }
}
