<?php

declare(strict_types=1);

namespace App\Services\Api\Global\PreOngRegistration;

use App\Models\PreOngRegistration;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class StorePreOngRegistrationService extends BaseService
{
    public function run(array $data): PreOngRegistration
    {
        $attributes = array_merge(Arr::only($data, ['name', 'email', 'phone', 'description', 'cnpj', 'city', 'state']), ['status' => 'pending']);

        return DB::transaction(fn (): PreOngRegistration => PreOngRegistration::create($attributes));
    }
}
