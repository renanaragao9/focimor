<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\Pet;

use App\Models\Pet;
use App\Services\BaseService;
use App\Traits\SortableTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexPetService extends BaseService
{
    use SortableTrait;

    public function run(array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'desc';
        $search = $data['search'] ?? null;
        $speciesId = $data['filters']['species_id'] ?? null;
        $goalId = $data['filters']['goal_id'] ?? null;
        $isActive = $data['filters']['is_active'] ?? null;

        $query = Pet::query()
            ->where('user_id', $this->getUserId())
            ->with(['species', 'breed', 'size', 'goal', 'photos'])
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($speciesId, fn ($q, $id) => $q->where('species_id', $id))
            ->when($goalId, fn ($q, $id) => $q->where('goal_id', $id))
            ->when($isActive !== null, fn ($q) => $q->where('is_active', (bool) $isActive));

        $query = $this->applySorting($query, $sort, $direction, 'created_at', ['name', 'birth_date', 'created_at']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
