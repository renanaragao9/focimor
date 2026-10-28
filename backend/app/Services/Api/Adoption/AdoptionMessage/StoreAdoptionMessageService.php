<?php

declare(strict_types=1);

namespace App\Services\Api\Adoption\AdoptionMessage;

use App\Models\AdoptionMatch;
use App\Models\Message;
use App\Models\User;
use App\Notifications\Adoption\NewAdoptionMessageNotification;
use Illuminate\Support\Facades\DB;

class StoreAdoptionMessageService extends AdoptionChatService
{
    public function run(AdoptionMatch $adoptionMatch, array $data): Message
    {
        $body = $data['body'];

        $this->ensureChatIsOpen($adoptionMatch, ['accepted']);

        $senderId = $this->getUserId();

        $message = DB::transaction(fn (): Message => Message::create([
            'body' => $body,
            'adoption_match_id' => $adoptionMatch->id,
            'sender_user_id' => $senderId,
        ]));

        $this->notifyRecipients($adoptionMatch, $senderId);

        return $message;
    }

    private function notifyRecipients(AdoptionMatch $adoptionMatch, int $senderId): void
    {
        $unreadFromSender = Message::where('adoption_match_id', $adoptionMatch->id)->where('sender_user_id', $senderId)->whereNull('read_at')->count();

        if ($unreadFromSender !== 1) {
            return;
        }

        $adoptionMatch->load('pet');
        $pet = $adoptionMatch->pet;

        $recipients = $senderId === $adoptionMatch->user_id
            ? $pet->managers()->get()
            : User::whereKey($adoptionMatch->user_id)->get();

        $recipients
            ->reject(fn (User $user) => $user->id === $senderId)
            ->each(fn (User $user) => $user->notify(new NewAdoptionMessageNotification($adoptionMatch->uuid, $pet->name)));
    }
}
