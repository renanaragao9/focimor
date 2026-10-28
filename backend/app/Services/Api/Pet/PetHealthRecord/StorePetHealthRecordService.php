<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetHealthRecord;

use App\Models\Pet;
use App\Models\PetHealthRecord;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class StorePetHealthRecordService extends BaseService
{
    public function run(Pet $pet, array $data): PetHealthRecord
    {
        return DB::transaction(fn (): PetHealthRecord => $pet->healthRecords()->create($data)->load('type'));
    }
}
