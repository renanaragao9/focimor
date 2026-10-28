<?php

declare(strict_types=1);

namespace App\Traits;

trait ResolvesCampaignStatus
{
    protected function statusFor(float|string $raised, float|string $goal): string
    {
        return (float) $raised >= (float) $goal ? 'completed' : 'active';
    }
}
