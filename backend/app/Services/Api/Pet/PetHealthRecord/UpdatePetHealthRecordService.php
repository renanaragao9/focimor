<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetHealthRecord;

use App\Models\PetHealthRecord;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class UpdatePetHealthRecordService extends BaseService
{
    public function run(PetHealthRecord $healthRecord, array $data): PetHealthRecord
    {
        return DB::transaction(function () use ($healthRecord, $data): PetHealthRecord {
            $healthRecord->update($data);

            return $healthRecord->refresh()->load('type');
        });
    }
}
