<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngCampaign;

use App\Models\OngCampaign;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class DeleteOngCampaignService extends BaseService
{
    public function run(OngCampaign $campaign): void
    {
        DB::transaction(fn () => $campaign->delete());
    }
}
