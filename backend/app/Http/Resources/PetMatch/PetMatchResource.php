<?php

declare(strict_types=1);

namespace App\Http\Resources\PetMatch;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetMatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userId = $request->user()->id;
        $mine = $this->petOne->user_id === $userId ? $this->petOne : $this->petTwo;
        $other = $mine->is($this->petOne) ? $this->petTwo : $this->petOne;

        return [
            'uuid' => $this->uuid,
            'matched_at' => $this->matched_at?->format('d/m/Y H:i:s'),
            'my_pet' => ['uuid' => $mine->uuid, 'name' => $mine->name],
            'other_pet' => new PublicPetResource($other),
            'unread_count' => $this->when($this->resource->getAttribute('unread_count') !== null, fn () => (int) $this->unread_count),
            'last_message' => $this->whenLoaded('latestMessage', fn () => $this->latestMessage ? new PetMessageResource($this->latestMessage) : null),
        ];
    }
}
