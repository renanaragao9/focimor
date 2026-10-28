<?php

namespace App\Notifications\Pet;

use Illuminate\Notifications\Notification;

class NewPetMessageNotification extends Notification
{
    public function __construct(public string $matchUuid, public string $fromPetName) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_message',
            'title' => 'Nova mensagem',
            'body' => "{$this->fromPetName} enviou uma mensagem.",
            'kind' => 'pet_match',
            'match_uuid' => $this->matchUuid,
        ];
    }
}
