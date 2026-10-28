<?php

declare(strict_types=1);

namespace App\Http\Resources\Notification;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = $this->data;
        $type = $data['type'] ?? null;
        $title = $data['title'] ?? null;
        $body = $data['body'] ?? null;

        return [
            'id' => $this->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => collect($data)->except(['type', 'title', 'body'])->all(),
            'is_read' => $this->read_at !== null,
            'read_at' => $this->read_at?->format('d/m/Y H:i:s'),
            'created_at' => $this->created_at?->format('d/m/Y H:i:s'),
        ];
    }
}
