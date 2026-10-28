<?php

namespace App\Filament\Resources\PetPhotos\Pages;

use App\Filament\Resources\PetPhotos\PetPhotoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditPetPhoto extends EditRecord
{
    protected static string $resource = PetPhotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon(Heroicon::OutlinedEye),
            DeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
        ];
    }
}
