<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\Ong;

use App\Models\Ong;
use App\Services\BaseService;
use App\Traits\SortableTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexOngService extends BaseService
{
    use SortableTrait;

    public function run(array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'asc';
        $search = $data['search'] ?? null;
        $state = $data['filters']['state'] ?? null;
        $city = $data['filters']['city'] ?? null;

        $query = Ong::query()
            ->where('status', 'verified')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($state, fn ($q, $state) => $q->where('state', strtoupper($state)))
            ->when($city, fn ($q, $city) => $q->where('city', 'like', "%{$city}%"))
            ->withCount(['pets as adoptable_pets_count' => fn ($q) => $q->where('is_active', true)->whereHas('goal', fn ($g) => $g->where('slug', 'adoption'))]);

        $query = $this->applySorting($query, $sort, $direction, 'name', ['name', 'created_at']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
