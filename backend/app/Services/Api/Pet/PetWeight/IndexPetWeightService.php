<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetWeight;

use App\Models\Pet;
use App\Models\PetWeight;
use App\Services\BaseService;
use App\Traits\SortableTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexPetWeightService extends BaseService
{
    use SortableTrait;

    public function run(Pet $pet, array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'desc';

        $query = PetWeight::query()->where('pet_id', $pet->id);

        $query = $this->applySorting($query, $sort, $direction, 'measured_at', ['measured_at', 'weight_kg', 'created_at']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
