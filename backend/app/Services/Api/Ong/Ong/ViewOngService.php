<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\Ong;

use App\Models\Ong;
use App\Services\BaseService;

class ViewOngService extends BaseService
{
    public function run(Ong $ong): Ong
    {
        return $ong->loadCount(['pets as adoptable_pets_count' => fn ($q) => $q->where('is_active', true)->whereHas('goal', fn ($g) => $g->where('slug', 'adoption'))]);
    }
}
