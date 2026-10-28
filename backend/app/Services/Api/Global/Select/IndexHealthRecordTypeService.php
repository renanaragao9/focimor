<?php

declare(strict_types=1);

namespace App\Services\Api\Global\Select;

use App\Models\HealthRecordType;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;

class IndexHealthRecordTypeService extends BaseService
{
    public function run(): Collection
    {
        return HealthRecordType::query()
            ->select('id', 'name', 'slug')
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();
    }
}
