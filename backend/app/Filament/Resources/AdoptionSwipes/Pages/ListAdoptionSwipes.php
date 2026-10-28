<?php

namespace App\Filament\Resources\AdoptionSwipes\Pages;

use App\Filament\Resources\AdoptionSwipes\AdoptionSwipeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListAdoptionSwipes extends ListRecords
{
    protected static string $resource = AdoptionSwipeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::OutlinedPlus),
        ];
    }
}
