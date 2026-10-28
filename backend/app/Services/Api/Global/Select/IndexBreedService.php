<?php

declare(strict_types=1);

namespace App\Services\Api\Global\Select;

use App\Models\Breed;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;

class IndexBreedService extends BaseService
{
    private const LIMIT = 20;

    public function run(array $data): Collection
    {
        $search = trim((string) ($data['search'] ?? ''));
        $speciesId = $data['species_id'] ?? null;

        return Breed::query()
            ->select('id', 'name', 'slug', 'species_id')
            ->where('is_active', true)
            ->when($speciesId, fn ($query, $speciesId) => $query->where('species_id', $speciesId))
            ->when($search !== '', fn ($query) => $query
                ->where('name', 'like', '%'.addcslashes($search, '%_\\').'%')
                ->orderByRaw('case when name like ? then 0 else 1 end', [addcslashes($search, '%_\\').'%']))
            ->orderBy('order')
            ->orderBy('name')
            ->limit(self::LIMIT)
            ->get();
    }
}
