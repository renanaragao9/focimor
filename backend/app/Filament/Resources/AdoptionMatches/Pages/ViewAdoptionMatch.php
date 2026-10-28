<?php

namespace App\Filament\Resources\AdoptionMatches\Pages;

use App\Filament\Resources\AdoptionMatches\AdoptionMatchResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewAdoptionMatch extends ViewRecord
{
    protected static string $resource = AdoptionMatchResource::class;

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
