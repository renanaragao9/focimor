<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngCampaign;

use App\Models\Ong;
use App\Models\OngCampaign;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexOngCampaignService extends BaseService
{
    public function run(Ong $managedOng, array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $status = $data['filters']['status'] ?? null;

        return OngCampaign::query()
            ->where('ong_id', $managedOng->id)
            ->when($status, fn ($q, $value) => $q->where('status', $value))
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
