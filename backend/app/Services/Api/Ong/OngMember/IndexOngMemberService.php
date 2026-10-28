<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngMember;

use App\Models\Ong;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;

class IndexOngMemberService extends BaseService
{
    public function run(Ong $managedOng): Collection
    {
        return $managedOng->users()->orderBy('name')->get();
    }
}
