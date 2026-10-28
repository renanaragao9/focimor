<?php

declare(strict_types=1);

namespace App\Services\Api\PetMatch\PetMessage;

use App\Models\Message;
use App\Models\PetMatch;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class ReadPetMessageService extends BaseService
{
    public function run(PetMatch $petMatch): int
    {
        return DB::transaction(fn (): int => Message::query()
            ->where('pet_match_id', $petMatch->id)
            ->where('sender_user_id', '!=', $this->getUserId())
            ->whereNull('read_at')
            ->update(['read_at' => now()]));
    }
}
