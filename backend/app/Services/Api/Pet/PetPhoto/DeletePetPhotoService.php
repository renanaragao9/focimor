<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetPhoto;

use App\Models\PetPhoto;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class DeletePetPhotoService extends BaseService
{
    public function run(PetPhoto $photo): void
    {
        DB::transaction(function () use ($photo): void {
            $wasPrimary = $photo->is_primary;
            $photo->delete();

            if ($wasPrimary) {
                PetPhoto::where('pet_id', $photo->pet_id)->orderBy('order')->orderBy('id')->first()?->update(['is_primary' => true]);
            }
        });
    }
}
