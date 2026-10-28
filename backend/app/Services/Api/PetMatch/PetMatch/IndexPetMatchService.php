<?php

declare(strict_types=1);

namespace App\Services\Api\PetMatch\PetMatch;

use App\Models\PetMatch;
use App\Services\BaseService;
use App\Traits\SortableTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexPetMatchService extends BaseService
{
    use SortableTrait;

    public function run(array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'desc';
        $petUuid = $data['filters']['pet_uuid'] ?? null;

        $userId = $this->getUserId();

        $query = PetMatch::query()
            ->where('is_active', true)
            ->whereHas('petOne')
            ->whereHas('petTwo')
            ->where(fn ($q) => $q
                ->whereHas('petOne', fn ($p) => $p->where('user_id', $userId))
                ->orWhereHas('petTwo', fn ($p) => $p->where('user_id', $userId)))
            ->when($petUuid, fn ($q) => $q->where(fn ($w) => $w
                ->whereHas('petOne', fn ($p) => $p->where('uuid', $petUuid)->where('user_id', $userId))
                ->orWhereHas('petTwo', fn ($p) => $p->where('uuid', $petUuid)->where('user_id', $userId))))
            ->with(['petOne.species', 'petOne.photos', 'petTwo.species', 'petTwo.photos', 'latestMessage'])
            ->withCount(['messages as unread_count' => fn ($q) => $q->whereNull('read_at')->where('sender_user_id', '!=', $userId)]);

        $query = $this->applySorting($query, $sort, $direction, 'matched_at', ['matched_at', 'created_at']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
