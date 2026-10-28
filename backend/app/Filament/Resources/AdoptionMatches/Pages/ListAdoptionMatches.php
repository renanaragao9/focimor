<?php

namespace App\Filament\Resources\AdoptionMatches\Pages;

use App\Filament\Resources\AdoptionMatches\AdoptionMatchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListAdoptionMatches extends ListRecords
{
    protected static string $resource = AdoptionMatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::OutlinedPlus),
        ];
    }
}
