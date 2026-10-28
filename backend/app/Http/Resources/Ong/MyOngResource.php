<?php

declare(strict_types=1);

namespace App\Http\Resources\Ong;

use App\Helpers\GenerateTemporaryUrlHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyOngResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'cnpj' => $this->cnpj,
            'description' => $this->description,
            'logo_url' => $this->logo_path ? GenerateTemporaryUrlHelper::run($this->logo_path) : null,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'pix_key' => $this->pix_key,
            'donation_info' => $this->donation_info,
            'status' => $this->status,
            'verified_at' => $this->verified_at?->format('d/m/Y H:i:s'),
            'my_role' => $this->pivot?->role ?? $this->roleOf($request->user()->id),
        ];
    }
}
