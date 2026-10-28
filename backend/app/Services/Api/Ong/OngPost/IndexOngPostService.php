<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngPost;

use App\Models\Ong;
use App\Models\OngPost;
use App\Services\BaseService;
use App\Traits\SortableTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexOngPostService extends BaseService
{
    use SortableTrait;

    public function run(Ong $managedOng, array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'desc';
        $search = $data['search'] ?? null;
        $published = $data['filters']['published'] ?? null;

        $query = OngPost::query()
            ->where('ong_id', $managedOng->id)
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($published !== null, fn ($q) => (bool) $published
                ? $q->whereNotNull('published_at')->where('published_at', '<=', now())
                : $q->where(fn ($w) => $w->whereNull('published_at')->orWhere('published_at', '>', now())))
            ->with(['ong', 'pet']);

        $query = $this->applySorting($query, $sort, $direction, 'created_at', ['published_at', 'created_at', 'title']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
