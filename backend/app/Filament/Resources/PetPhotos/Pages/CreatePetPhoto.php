<?php

namespace App\Filament\Resources\PetPhotos\Pages;

use App\Filament\Resources\PetPhotos\PetPhotoResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePetPhoto extends CreateRecord
{
    protected static string $resource = PetPhotoResource::class;
}
