<?php

declare(strict_types=1);

namespace App\Services\Api\Global\Select;

use App\Models\Goal;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;

class IndexGoalService extends BaseService
{
    public function run(): Collection
    {
        return Goal::query()
            ->select('id', 'name', 'slug')
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();
    }
}
