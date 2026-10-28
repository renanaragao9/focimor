<?php

declare(strict_types=1);

namespace App\Services\Api\Notification\Notification;

use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexNotificationService extends BaseService
{
    public function run(array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 20);
        $unread = $data['filters']['unread'] ?? null;

        $query = $this->getUser()->notifications()
            ->when($unread !== null, fn ($q) => (bool) $unread ? $q->whereNull('read_at') : $q->whereNotNull('read_at'))
            ->orderByDesc('created_at');

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
