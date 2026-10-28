<?php

namespace App\Filament\Resources\AdoptionSwipes\Pages;

use App\Filament\Resources\AdoptionSwipes\AdoptionSwipeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewAdoptionSwipe extends ViewRecord
{
    protected static string $resource = AdoptionSwipeResource::class;

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
