<?php

declare(strict_types=1);

namespace App\Services\Api\Adoption\AdoptionMessage;

use App\Models\AdoptionMatch;
use App\Models\Message;
use App\Traits\SortableTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexAdoptionMessageService extends AdoptionChatService
{
    use SortableTrait;

    public function run(AdoptionMatch $adoptionMatch, array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 30);
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'desc';

        $this->ensureChatIsOpen($adoptionMatch, ['accepted', 'adopted']);

        $query = Message::query()->where('adoption_match_id', $adoptionMatch->id);

        $query = $this->applySorting($query, $sort, $direction, 'created_at', ['created_at']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
