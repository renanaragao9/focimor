<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ong;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Ong\OngPublicCampaign\IndexOngPublicCampaignRequest;
use App\Http\Resources\Ong\OngCampaignResource;
use App\Models\Ong;
use App\Services\Api\Ong\OngPublicCampaign\IndexOngPublicCampaignService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OngPublicCampaignController extends BaseController
{
    public function index(
        Ong $ong,
        IndexOngPublicCampaignRequest $indexOngPublicCampaignRequest,
        IndexOngPublicCampaignService $indexOngPublicCampaignService
    ): AnonymousResourceCollection {
        $data = $indexOngPublicCampaignRequest->validated();
        $campaigns = $indexOngPublicCampaignService->run($ong, $data);

        return OngCampaignResource::collection($campaigns);
    }
}
