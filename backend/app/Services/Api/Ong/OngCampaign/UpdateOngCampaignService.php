<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngCampaign;

use App\Models\Ong;
use App\Models\OngCampaign;
use App\Services\BaseService;
use App\Traits\AuthorizesOngActions;
use App\Traits\ResolvesCampaignStatus;
use Illuminate\Support\Facades\DB;

class UpdateOngCampaignService extends BaseService
{
    use AuthorizesOngActions, ResolvesCampaignStatus;

    public function run(Ong $managedOng, OngCampaign $campaign, array $data): OngCampaign
    {
        $goalAmount = $data['goal_amount'] ?? $campaign->goal_amount;
        $raisedAmount = $data['raised_amount'] ?? $campaign->raised_amount;
        $status = $data['status'] ?? null;

        $this->ensureOngVerified($managedOng);

        $keepsStatus = $status !== null || $campaign->status === 'cancelled';
        $attributes = $keepsStatus ? $data : array_merge($data, ['status' => $this->statusFor($raisedAmount, $goalAmount)]);

        return DB::transaction(function () use ($campaign, $attributes): OngCampaign {
            $campaign->update($attributes);

            return $campaign->refresh()->load('ong');
        });
    }
}
