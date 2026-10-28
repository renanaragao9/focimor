<?php

namespace App\Filament\Resources\PetPhotos\Pages;

use App\Filament\Resources\PetPhotos\PetPhotoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewPetPhoto extends ViewRecord
{
    protected static string $resource = PetPhotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon(Heroicon::OutlinedPencil),
            DeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
        ];
    }
}
