<?php

declare(strict_types=1);

namespace App\Services\Api\Adoption\AdoptionMessage;

use App\Models\AdoptionMatch;
use App\Services\BaseService;
use Illuminate\Validation\ValidationException;

abstract class AdoptionChatService extends BaseService
{
    protected function ensureChatIsOpen(AdoptionMatch $adoptionMatch, array $statuses): void
    {
        if (! in_array($adoptionMatch->status, $statuses, true)) {
            throw ValidationException::withMessages(['adoption' => ['O chat só fica disponível depois que o dono aceita o interesse.']]);
        }
    }
}
