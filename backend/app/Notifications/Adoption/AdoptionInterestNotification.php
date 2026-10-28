<?php

namespace App\Notifications\Adoption;

use Illuminate\Notifications\Notification;

class AdoptionInterestNotification extends Notification
{
    public function __construct(public string $adoptionUuid, public string $petName, public string $adopterName) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'adoption_interest',
            'title' => "Novo interesse em {$this->petName}",
            'body' => "{$this->adopterName} quer adotar {$this->petName}.",
            'adoption_uuid' => $this->adoptionUuid,
        ];
    }
}
