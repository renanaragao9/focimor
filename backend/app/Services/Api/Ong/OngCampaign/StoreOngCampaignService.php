<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngCampaign;

use App\Models\Ong;
use App\Models\OngCampaign;
use App\Services\BaseService;
use App\Traits\AuthorizesOngActions;
use App\Traits\ResolvesCampaignStatus;
use Illuminate\Support\Facades\DB;

class StoreOngCampaignService extends BaseService
{
    use AuthorizesOngActions, ResolvesCampaignStatus;

    public function run(Ong $managedOng, array $data): OngCampaign
    {
        $title = $data['title'];
        $description = $data['description'] ?? null;
        $goalAmount = $data['goal_amount'];
        $raisedAmount = $data['raised_amount'] ?? 0;
        $endsAt = $data['ends_at'] ?? null;

        $this->ensureOngVerified($managedOng);

        return DB::transaction(function () use ($managedOng, $title, $description, $goalAmount, $raisedAmount, $endsAt): OngCampaign {
            $campaign = $managedOng->campaigns()->create([
                'title' => $title,
                'description' => $description,
                'goal_amount' => $goalAmount,
                'raised_amount' => $raisedAmount,
                'status' => $this->statusFor($raisedAmount, $goalAmount),
                'ends_at' => $endsAt,
            ]);

            return $campaign->refresh()->load('ong');
        });
    }
}
