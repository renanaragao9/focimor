<?php

namespace App\Notifications\Adoption;

use Illuminate\Notifications\Notification;

class AdoptionDecisionNotification extends Notification
{
    public function __construct(public string $adoptionUuid, public string $petName, public string $status) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $messages = [
            'accepted' => ['Interesse aceito!', "O interesse em {$this->petName} foi aceito. Converse pelo chat."],
            'declined' => ['Interesse recusado', "O interesse em {$this->petName} foi recusado."],
            'adopted' => ['Adoção concluída!', "A adoção de {$this->petName} foi concluída."],
        ];

        $message = $messages[$this->status];
        [$title, $body] = $message;

        return [
            'type' => 'adoption_decision',
            'title' => $title,
            'body' => $body,
            'adoption_uuid' => $this->adoptionUuid,
            'status' => $this->status,
        ];
    }
}
