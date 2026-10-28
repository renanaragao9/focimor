<?php

declare(strict_types=1);

namespace App\Http\Resources\PetMatch;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'body' => $this->body,
            'is_mine' => $this->sender_user_id === $request->user()->id,
            'read_at' => $this->read_at?->format('d/m/Y H:i:s'),
            'created_at' => $this->created_at?->format('d/m/Y H:i:s'),
        ];
    }
}
