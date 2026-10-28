<?php

declare(strict_types=1);

namespace App\Http\Resources\Pet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetVaccineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'applied_at' => $this->applied_at?->format('d/m/Y'),
            'next_dose_at' => $this->next_dose_at?->format('d/m/Y'),
            'batch' => $this->batch,
            'veterinarian' => $this->veterinarian,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i:s'),
        ];
    }
}
