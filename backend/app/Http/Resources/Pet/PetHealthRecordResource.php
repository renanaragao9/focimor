<?php

declare(strict_types=1);

namespace App\Http\Resources\Pet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetHealthRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'performed_at' => $this->performed_at?->format('d/m/Y'),
            'next_due_at' => $this->next_due_at?->format('d/m/Y'),
            'veterinarian' => $this->veterinarian,
            'cost' => $this->cost,
            'description' => $this->description,
            'created_at' => $this->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i:s'),

            'type' => $this->whenLoaded('type', fn () => ['id' => $this->type->id, 'name' => $this->type->name]),
        ];
    }
}
