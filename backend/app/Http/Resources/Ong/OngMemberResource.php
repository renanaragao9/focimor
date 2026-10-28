<?php

declare(strict_types=1);

namespace App\Http\Resources\Ong;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OngMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->pivot->role,
            'joined_at' => $this->pivot->created_at?->format('d/m/Y H:i:s'),
        ];
    }
}
