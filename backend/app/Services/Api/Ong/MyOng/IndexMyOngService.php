<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\MyOng;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;

class IndexMyOngService extends BaseService
{
    public function run(): Collection
    {
        return $this->getUser()->ongs()->orderBy('name')->get();
    }
}
