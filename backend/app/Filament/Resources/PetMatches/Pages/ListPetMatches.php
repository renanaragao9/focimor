<?php

namespace App\Filament\Resources\PetMatches\Pages;

use App\Filament\Resources\PetMatches\PetMatchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListPetMatches extends ListRecords
{
    protected static string $resource = PetMatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::OutlinedPlus),
        ];
    }
}
