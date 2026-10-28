<?php

namespace App\Filament\Resources\PetMatches\Pages;

use App\Filament\Resources\PetMatches\PetMatchResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewPetMatch extends ViewRecord
{
    protected static string $resource = PetMatchResource::class;

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
