<?php

declare(strict_types=1);

namespace App\Http\Resources\Pet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetWeightResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'weight_kg' => $this->weight_kg,
            'height_cm' => $this->height_cm,
            'width_cm' => $this->width_cm,
            'measured_at' => $this->measured_at?->format('d/m/Y'),
            'created_at' => $this->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i:s'),
        ];
    }
}
