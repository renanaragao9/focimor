<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngAdoptablePet;

use App\Models\Ong;
use App\Models\Pet;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexOngAdoptablePetService extends BaseService
{
    public function run(Ong $ong, array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $speciesId = $data['filters']['species_id'] ?? null;
        $gender = $data['filters']['gender'] ?? null;

        return Pet::query()
            ->where('ong_id', $ong->id)
            ->where('is_active', true)
            ->whereHas('goal', fn ($q) => $q->where('slug', 'adoption'))
            ->when($speciesId, fn ($q, $id) => $q->where('species_id', $id))
            ->when($gender, fn ($q, $gender) => $q->where('gender', $gender))
            ->with(['species', 'breed', 'size', 'goal', 'photos'])
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
