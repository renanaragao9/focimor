<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\MyOng;

use App\Models\Ong;
use App\Services\BaseService;

class ViewMyOngService extends BaseService
{
    public function run(Ong $managedOng): Ong
    {
        return $managedOng;
    }
}
