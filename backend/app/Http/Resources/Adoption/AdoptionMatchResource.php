<?php

declare(strict_types=1);

namespace App\Http\Resources\Adoption;

use App\Http\Resources\PetMatch\PetMessageResource;
use App\Http\Resources\PetMatch\PublicPetResource;
use App\Models\AdoptionMatch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdoptionMatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'status' => $this->status,
            'status_label' => AdoptionMatch::STATUS_LABELS[$this->status] ?? $this->status,
            'role' => $this->user_id === $request->user()->id ? 'adopter' : 'owner',
            'created_at' => $this->created_at?->format('d/m/Y H:i:s'),
            'decided_at' => $this->decided_at?->format('d/m/Y H:i:s'),
            'pet' => $this->whenLoaded('pet', fn () => new PublicPetResource($this->pet)),
            'adopter' => $this->whenLoaded('user', fn () => ['name' => $this->user->name]),
            'unread_count' => $this->when($this->resource->getAttribute('unread_count') !== null, fn () => (int) $this->unread_count),
            'last_message' => $this->whenLoaded('latestMessage', fn () => $this->latestMessage ? new PetMessageResource($this->latestMessage) : null),
        ];
    }
}
