<?php

namespace App\Notifications\Adoption;

use Illuminate\Notifications\Notification;

class NewAdoptionMessageNotification extends Notification
{
    public function __construct(public string $adoptionUuid, public string $petName) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_message',
            'title' => 'Nova mensagem',
            'body' => "Você recebeu uma mensagem sobre {$this->petName}.",
            'kind' => 'adoption',
            'adoption_uuid' => $this->adoptionUuid,
        ];
    }
}
