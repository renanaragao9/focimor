<?php

declare(strict_types=1);

namespace App\Http\Resources\Ong;

use App\Models\OngCampaign;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OngCampaignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'goal_amount' => $this->goal_amount,
            'raised_amount' => $this->raised_amount,
            'remaining_amount' => number_format($this->remainingAmount(), 2, '.', ''),
            'progress_percent' => $this->progressPercent(),
            'status' => $this->status,
            'status_label' => OngCampaign::STATUS_LABELS[$this->status] ?? $this->status,
            'ends_at' => $this->ends_at?->format('d/m/Y'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i:s'),
            'ong' => $this->whenLoaded('ong', fn () => $this->ong ? ['uuid' => $this->ong->uuid, 'name' => $this->ong->name] : null),
        ];
    }
}
