<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\MyOng;

use App\Models\Ong;
use App\Services\BaseService;
use App\Traits\AuthorizesOngActions;
use Illuminate\Support\Facades\DB;

class UpdateMyOngService extends BaseService
{
    use AuthorizesOngActions;

    public function run(Ong $managedOng, array $data): Ong
    {
        $this->ensureOngAdmin($managedOng);

        return DB::transaction(function () use ($managedOng, $data): Ong {
            $managedOng->update($data);

            return $managedOng->refresh();
        });
    }
}
