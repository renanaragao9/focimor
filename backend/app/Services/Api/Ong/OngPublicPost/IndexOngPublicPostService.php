<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngPublicPost;

use App\Models\Ong;
use App\Models\OngPost;
use App\Services\BaseService;
use App\Traits\SortableTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexOngPublicPostService extends BaseService
{
    use SortableTrait;

    public function run(Ong $ong, array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'desc';
        $search = $data['search'] ?? null;

        $query = OngPost::query()
            ->published()
            ->where('ong_id', $ong->id)
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->with(['ong', 'pet']);

        $query = $this->applySorting($query, $sort, $direction, 'published_at', ['published_at', 'title']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
