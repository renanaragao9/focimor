<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ong;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Ong\OngCampaign\IndexOngCampaignRequest;
use App\Http\Requests\Api\Ong\OngCampaign\StoreOngCampaignRequest;
use App\Http\Requests\Api\Ong\OngCampaign\UpdateOngCampaignRequest;
use App\Http\Resources\Ong\OngCampaignResource;
use App\Models\Ong;
use App\Models\OngCampaign;
use App\Services\Api\Ong\OngCampaign\DeleteOngCampaignService;
use App\Services\Api\Ong\OngCampaign\IndexOngCampaignService;
use App\Services\Api\Ong\OngCampaign\StoreOngCampaignService;
use App\Services\Api\Ong\OngCampaign\UpdateOngCampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OngCampaignController extends BaseController
{
    public function index(
        Ong $managedOng,
        IndexOngCampaignRequest $indexOngCampaignRequest,
        IndexOngCampaignService $indexOngCampaignService
    ): AnonymousResourceCollection {
        $data = $indexOngCampaignRequest->validated();
        $campaigns = $indexOngCampaignService->run($managedOng, $data);

        return OngCampaignResource::collection($campaigns);
    }

    public function store(
        Ong $managedOng,
        StoreOngCampaignRequest $storeOngCampaignRequest,
        StoreOngCampaignService $storeOngCampaignService
    ): JsonResponse {
        $data = $storeOngCampaignRequest->validated();
        $campaign = $storeOngCampaignService->run($managedOng, $data);

        return $this->successResponse(new OngCampaignResource($campaign), 'Caixinha criada com sucesso.', 201);
    }

    public function update(
        Ong $managedOng,
        OngCampaign $campaign,
        UpdateOngCampaignRequest $updateOngCampaignRequest,
        UpdateOngCampaignService $updateOngCampaignService
    ): JsonResponse {
        $data = $updateOngCampaignRequest->validated();
        $campaign = $updateOngCampaignService->run($managedOng, $campaign, $data);

        return $this->successResponse(new OngCampaignResource($campaign), 'Caixinha atualizada com sucesso.');
    }

    public function destroy(
        Ong $managedOng,
        OngCampaign $campaign,
        DeleteOngCampaignService $deleteOngCampaignService
    ): JsonResponse {
        $deleteOngCampaignService->run($campaign);

        return $this->successResponse(null, 'Caixinha removida com sucesso.');
    }
}
