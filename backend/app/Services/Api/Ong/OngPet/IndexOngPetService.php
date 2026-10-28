<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngPet;

use App\Models\Ong;
use App\Models\Pet;
use App\Services\BaseService;
use App\Traits\SortableTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexOngPetService extends BaseService
{
    use SortableTrait;

    public function run(Ong $managedOng, array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'desc';
        $search = $data['search'] ?? null;
        $isActive = $data['filters']['is_active'] ?? null;

        $query = Pet::query()
            ->where('ong_id', $managedOng->id)
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($isActive !== null, fn ($q) => $q->where('is_active', (bool) $isActive))
            ->with(['species', 'breed', 'size', 'goal', 'photos']);

        $query = $this->applySorting($query, $sort, $direction, 'created_at', ['name', 'created_at']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
