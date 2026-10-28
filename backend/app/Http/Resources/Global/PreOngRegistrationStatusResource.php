<?php

declare(strict_types=1);

namespace App\Http\Resources\Global;

use App\Models\PreOngRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PreOngRegistrationStatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->status,
            'status_label' => PreOngRegistration::STATUS_LABELS[$this->status] ?? $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
