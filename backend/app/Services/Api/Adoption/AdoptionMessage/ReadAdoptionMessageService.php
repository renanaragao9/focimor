<?php

declare(strict_types=1);

namespace App\Services\Api\Adoption\AdoptionMessage;

use App\Models\AdoptionMatch;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class ReadAdoptionMessageService extends AdoptionChatService
{
    public function run(AdoptionMatch $adoptionMatch): int
    {
        $this->ensureChatIsOpen($adoptionMatch, ['accepted', 'adopted']);

        return DB::transaction(fn (): int => Message::query()
            ->where('adoption_match_id', $adoptionMatch->id)
            ->where('sender_user_id', '!=', $this->getUserId())
            ->whereNull('read_at')
            ->update(['read_at' => now()]));
    }
}
