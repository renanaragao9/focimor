<?php

declare(strict_types=1);

namespace App\Services\Api\Adoption\Adoption;

use App\Models\AdoptionMatch;
use App\Services\BaseService;
use App\Traits\SortableTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexReceivedAdoptionService extends BaseService
{
    use SortableTrait;

    public function run(array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'desc';
        $status = $data['filters']['status'] ?? null;
        $petUuid = $data['filters']['pet_uuid'] ?? null;

        $userId = $this->getUserId();

        $query = AdoptionMatch::query()
            ->whereHas('pet', fn ($p) => $p->managedBy($userId)->when($petUuid, fn ($q) => $q->where('uuid', $petUuid)))
            ->when($status, fn ($q, $status) => $q->where('status', $status))
            ->with(['pet.species', 'pet.breed', 'pet.size', 'pet.goal', 'pet.photos', 'pet.ong', 'user', 'latestMessage'])
            ->withCount(['messages as unread_count' => fn ($q) => $q->whereNull('read_at')->where('sender_user_id', '!=', $userId)]);

        $query = $this->applySorting($query, $sort, $direction, 'created_at', ['created_at', 'decided_at']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
