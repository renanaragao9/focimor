<?php

declare(strict_types=1);

namespace App\Services\Api\PetMatch\PetMatch;

use App\Models\PetMatch;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class DeletePetMatchService extends BaseService
{
    public function run(PetMatch $petMatch): void
    {
        DB::transaction(fn () => $petMatch->update(['is_active' => false]));
    }
}
