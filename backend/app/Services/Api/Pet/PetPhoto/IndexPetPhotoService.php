<?php

declare(strict_types=1);

namespace App\Services\Api\Pet\PetPhoto;

use App\Models\Pet;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;

class IndexPetPhotoService extends BaseService
{
    public function run(Pet $pet): Collection
    {
        return $pet->photos()->orderBy('order')->orderBy('id')->get();
    }
}
