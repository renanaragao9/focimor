<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetVaccine;

use App\Models\Pet;
use App\Models\PetVaccine;
use App\Services\BaseService;
use App\Traits\SortableTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexPetVaccineService extends BaseService
{
    use SortableTrait;

    public function run(Pet $pet, array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'desc';
        $search = $data['search'] ?? null;

        $query = PetVaccine::query()->where('pet_id', $pet->id)
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"));

        $query = $this->applySorting($query, $sort, $direction, 'applied_at', ['applied_at', 'next_dose_at', 'name', 'created_at']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
