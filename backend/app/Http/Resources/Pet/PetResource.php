<?php

declare(strict_types=1);

namespace App\Http\Resources\Pet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'gender' => $this->gender,
            'color' => $this->color,
            'bio' => $this->bio,
            'birth_date' => $this->birth_date?->format('d/m/Y'),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_neutered' => $this->is_neutered,
            'is_vaccinated' => $this->is_vaccinated,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i:s'),

            'species' => $this->whenLoaded('species', fn () => ['id' => $this->species->id, 'name' => $this->species->name]),
            'breed' => $this->whenLoaded('breed', fn () => $this->breed ? ['id' => $this->breed->id, 'name' => $this->breed->name] : null),
            'size' => $this->whenLoaded('size', fn () => $this->size ? ['id' => $this->size->id, 'name' => $this->size->name] : null),
            'goal' => $this->whenLoaded('goal', fn () => ['id' => $this->goal->id, 'name' => $this->goal->name]),
            'photos' => $this->whenLoaded('photos', fn () => PetPhotoResource::collection($this->photos)),
            'detail' => $this->whenLoaded('detail', fn () => $this->detail ? new PetDetailResource($this->detail) : null),
        ];
    }
}
