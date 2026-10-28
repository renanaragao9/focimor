<?php

declare(strict_types=1);

namespace App\Http\Resources\Ong;

use App\Helpers\GenerateTemporaryUrlHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicOngResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'logo_url' => $this->logo_path ? GenerateTemporaryUrlHelper::run($this->logo_path) : null,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'pix_key' => $this->pix_key,
            'donation_info' => $this->donation_info,
            'is_verified' => $this->status === 'verified',
            'adoptable_pets_count' => $this->when($this->resource->getAttribute('adoptable_pets_count') !== null, fn () => (int) $this->adoptable_pets_count),
        ];
    }
}
