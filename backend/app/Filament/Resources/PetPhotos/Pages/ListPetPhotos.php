<?php

namespace App\Filament\Resources\PetPhotos\Pages;

use App\Filament\Resources\PetPhotos\PetPhotoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListPetPhotos extends ListRecords
{
    protected static string $resource = PetPhotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::OutlinedPlus),
        ];
    }
}
