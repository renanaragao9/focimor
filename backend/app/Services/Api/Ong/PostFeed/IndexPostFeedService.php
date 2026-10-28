<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\PostFeed;

use App\Models\OngPost;
use App\Services\BaseService;
use App\Traits\SortableTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexPostFeedService extends BaseService
{
    use SortableTrait;

    public function run(array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'desc';
        $search = $data['search'] ?? null;
        $ongUuid = $data['filters']['ong_uuid'] ?? null;
        $system = $data['filters']['system'] ?? null;

        $query = OngPost::query()
            ->published()
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($ongUuid, fn ($q, $uuid) => $q->whereHas('ong', fn ($ong) => $ong->where('uuid', $uuid)))
            ->when((bool) $system, fn ($q) => $q->whereNull('ong_id'))
            ->with(['ong', 'pet']);

        $query = $this->applySorting($query, $sort, $direction, 'published_at', ['published_at', 'title']);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
