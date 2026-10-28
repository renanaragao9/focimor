<?php

declare(strict_types=1);

namespace App\Http\Resources\PetMatch;

use App\Http\Resources\Pet\PetPhotoResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicPetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $distance = $this->resource->getAttribute('distance_km');

        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'gender' => $this->gender,
            'color' => $this->color,
            'bio' => $this->bio,
            'birth_date' => $this->birth_date?->format('d/m/Y'),
            'is_neutered' => $this->is_neutered,
            'is_vaccinated' => $this->is_vaccinated,
            'distance_km' => $distance === null ? null : round((float) $distance, 1),

            'species' => $this->whenLoaded('species', fn () => ['id' => $this->species->id, 'name' => $this->species->name]),
            'breed' => $this->whenLoaded('breed', fn () => $this->breed ? ['id' => $this->breed->id, 'name' => $this->breed->name] : null),
            'size' => $this->whenLoaded('size', fn () => $this->size ? ['id' => $this->size->id, 'name' => $this->size->name] : null),
            'goal' => $this->whenLoaded('goal', fn () => ['id' => $this->goal->id, 'name' => $this->goal->name]),
            'ong' => $this->whenLoaded('ong', fn () => $this->ong ? [
                'uuid' => $this->ong->uuid,
                'name' => $this->ong->name,
                'city' => $this->ong->city,
                'state' => $this->ong->state,
                'is_verified' => $this->ong->status === 'verified',
            ] : null),
            'photos' => $this->whenLoaded('photos', fn () => PetPhotoResource::collection($this->photos)),
        ];
    }
}
