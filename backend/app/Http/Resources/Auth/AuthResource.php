<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use App\Helpers\GenerateTemporaryUrlHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar_url' => $this->avatar_path ? GenerateTemporaryUrlHelper::run($this->avatar_path) : null,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'email_verified_at' => $this->email_verified_at?->format('d/m/Y H:i:s'),
            'created_at' => $this->created_at?->format('d/m/Y H:i:s'),
        ];
    }
}
