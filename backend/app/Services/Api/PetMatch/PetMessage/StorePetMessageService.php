<?php

declare(strict_types=1);

namespace App\Services\Api\PetMatch\PetMessage;

use App\Models\Message;
use App\Models\PetMatch;
use App\Models\User;
use App\Notifications\Pet\NewPetMessageNotification;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class StorePetMessageService extends BaseService
{
    public function run(PetMatch $petMatch, array $data): Message
    {
        $body = $data['body'];
        $userId = $this->getUserId();

        $message = DB::transaction(fn (): Message => Message::create([
            'body' => $body,
            'pet_match_id' => $petMatch->id,
            'sender_user_id' => $userId,
        ]));

        $this->notifyRecipient($petMatch, $userId);

        return $message;
    }

    private function notifyRecipient(PetMatch $petMatch, int $senderId): void
    {
        $unreadFromSender = Message::where('pet_match_id', $petMatch->id)->where('sender_user_id', $senderId)->whereNull('read_at')->count();

        if ($unreadFromSender !== 1) {
            return;
        }

        $petMatch->load(['petOne', 'petTwo']);
        [$mine, $theirs] = $petMatch->petOne->user_id === $senderId ? [$petMatch->petOne, $petMatch->petTwo] : [$petMatch->petTwo, $petMatch->petOne];

        User::find($theirs->user_id)?->notify(new NewPetMessageNotification($petMatch->uuid, $mine->name));
    }
}
