<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetPhoto;

use App\Models\PetPhoto;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class UpdatePetPhotoService extends BaseService
{
    public function run(PetPhoto $photo, array $data): PetPhoto
    {
        $isPrimary = $data['is_primary'] ?? false;

        return DB::transaction(function () use ($photo, $data, $isPrimary): PetPhoto {
            if ($isPrimary) {
                PetPhoto::where('pet_id', $photo->pet_id)->where('id', '!=', $photo->id)->update(['is_primary' => false]);
            }

            $photo->update($data);

            return $photo->refresh();
        });
    }
}
