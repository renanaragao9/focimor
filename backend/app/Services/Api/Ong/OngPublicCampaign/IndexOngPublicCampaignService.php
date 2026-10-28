<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngPublicCampaign;

use App\Models\Ong;
use App\Models\OngCampaign;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexOngPublicCampaignService extends BaseService
{
    public function run(Ong $ong, array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);

        return OngCampaign::query()
            ->where('ong_id', $ong->id)
            ->where('status', '!=', 'cancelled')
            ->orderByRaw("case status when 'active' then 0 else 1 end")
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
