<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\Pet;

use App\Models\Pet;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class DeletePetService extends BaseService
{
    public function run(Pet $pet): void
    {
        DB::transaction(fn () => $pet->delete());
    }
}
