<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetHealthRecord;

use App\Models\PetHealthRecord;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class DeletePetHealthRecordService extends BaseService
{
    public function run(PetHealthRecord $healthRecord): void
    {
        DB::transaction(fn () => $healthRecord->delete());
    }
}
