<?php

declare(strict_types=1);

namespace App\Services\Api\Adoption\Adoption;

use App\Models\AdoptionMatch;
use App\Services\BaseService;
use App\Traits\SortableTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexAdoptionService extends BaseService
{
    use SortableTrait;

    public function run(array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'desc';
        $status = $data['filters']['status'] ?? null;

        $userId = $this->getUserId();

        $query = AdoptionMatch::query()
            ->where('user_id', $userId)
            ->whereHas('pet')
            ->when($status, fn ($q, $status) => $q->where('status', $status))
            ->with(['pet.species', 'pet.breed', 'pet.size', 'pet.goal', 'pet.photos', 'pet.ong', 'latestMessage'])
            ->withCount(['messages as unread_count' => fn ($q) => $q->whereNull('read_at')->where('sender_user_id', '!=', $userId)]);

        $query = $this->applySorting($query, $sort, $direction, 'created_at', ['created_at', 'decided_at']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
