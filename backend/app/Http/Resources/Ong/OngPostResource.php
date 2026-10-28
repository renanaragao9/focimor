<?php

declare(strict_types=1);

namespace App\Http\Resources\Ong;

use App\Helpers\GenerateTemporaryUrlHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OngPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'body' => $this->body,
            'cover_url' => $this->cover_path ? GenerateTemporaryUrlHelper::run($this->cover_path) : null,
            'published_at' => $this->published_at?->format('d/m/Y H:i:s'),
            'is_published' => $this->published_at !== null && $this->published_at->lte(now()),
            'created_at' => $this->created_at?->format('d/m/Y H:i:s'),
            'ong' => $this->whenLoaded('ong', fn () => $this->ong ? [
                'uuid' => $this->ong->uuid,
                'name' => $this->ong->name,
                'logo_url' => $this->ong->logo_path ? GenerateTemporaryUrlHelper::run($this->ong->logo_path) : null,
            ] : null),
            'pet' => $this->whenLoaded('pet', fn () => $this->pet ? ['uuid' => $this->pet->uuid, 'name' => $this->pet->name] : null),
        ];
    }
}
