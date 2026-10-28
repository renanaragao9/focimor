<?php

namespace App\Notifications\Pet;

use Illuminate\Notifications\Notification;

class NewMatchNotification extends Notification
{
    public function __construct(public string $matchUuid, public string $myPetName, public string $otherPetName) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_match',
            'title' => 'É um match!',
            'body' => "{$this->myPetName} e {$this->otherPetName} deram match.",
            'match_uuid' => $this->matchUuid,
        ];
    }
}
