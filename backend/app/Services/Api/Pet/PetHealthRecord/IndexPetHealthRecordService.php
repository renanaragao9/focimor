<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetHealthRecord;

use App\Models\Pet;
use App\Models\PetHealthRecord;
use App\Services\BaseService;
use App\Traits\SortableTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexPetHealthRecordService extends BaseService
{
    use SortableTrait;

    public function run(Pet $pet, array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'desc';
        $search = $data['search'] ?? null;
        $healthRecordTypeId = $data['filters']['health_record_type_id'] ?? null;

        $query = PetHealthRecord::query()->where('pet_id', $pet->id)->with('type')
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($healthRecordTypeId, fn ($q, $id) => $q->where('health_record_type_id', $id));

        $query = $this->applySorting($query, $sort, $direction, 'performed_at', ['performed_at', 'next_due_at', 'title', 'created_at']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
