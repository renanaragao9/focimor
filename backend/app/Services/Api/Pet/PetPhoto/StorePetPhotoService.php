<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetPhoto;

use App\Models\Pet;
use App\Models\PetPhoto;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StorePetPhotoService extends BaseService
{
    public const MAX_PHOTOS = 10;

    public function run(Pet $pet, array $data): PetPhoto
    {
        $photo = $data['photo'];
        $requestedPrimary = $data['is_primary'] ?? false;
        $requestedOrder = $data['order'] ?? null;

        return DB::transaction(function () use ($pet, $photo, $requestedPrimary, $requestedOrder): PetPhoto {
            $count = $pet->photos()->count();

            if ($count >= self::MAX_PHOTOS) {
                throw ValidationException::withMessages([
                    'photo' => ['O pet já tem o máximo de '.self::MAX_PHOTOS.' fotos.'],
                ]);
            }

            $isPrimary = $requestedPrimary || $count === 0;

            if ($isPrimary) {
                $pet->photos()->update(['is_primary' => false]);
            }

            return $pet->photos()->create([
                'path' => $photo->store('pet-photos', config('filesystems.media')),
                'order' => $requestedOrder ?? ($count === 0 ? 0 : (int) $pet->photos()->max('order') + 1),
                'is_primary' => $isPrimary,
            ]);
        });
    }
}
