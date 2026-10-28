<?php

declare(strict_types=1);

namespace App\Services\Api\PetMatch\PetMessage;

use App\Models\Message;
use App\Models\PetMatch;
use App\Services\BaseService;
use App\Traits\SortableTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexPetMessageService extends BaseService
{
    use SortableTrait;

    public function run(PetMatch $petMatch, array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 30);
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'desc';

        $query = Message::query()->where('pet_match_id', $petMatch->id);

        $query = $this->applySorting($query, $sort, $direction, 'created_at', ['created_at']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
