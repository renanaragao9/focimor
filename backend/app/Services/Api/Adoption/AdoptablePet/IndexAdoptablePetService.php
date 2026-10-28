<?php

declare(strict_types=1);

namespace App\Services\Api\Adoption\AdoptablePet;

use App\Models\Pet;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexAdoptablePetService extends BaseService
{
    public function run(array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $search = $data['search'] ?? null;
        $speciesId = $data['filters']['species_id'] ?? null;
        $breedId = $data['filters']['breed_id'] ?? null;
        $sizeId = $data['filters']['size_id'] ?? null;
        $gender = $data['filters']['gender'] ?? null;

        return Pet::query()
            ->adoptable()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($speciesId, fn ($q, $id) => $q->where('species_id', $id))
            ->when($breedId, fn ($q, $id) => $q->where('breed_id', $id))
            ->when($sizeId, fn ($q, $id) => $q->where('size_id', $id))
            ->when($gender, fn ($q, $value) => $q->where('gender', $value))
            ->with(['species', 'breed', 'size', 'goal', 'photos', 'ong'])
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
