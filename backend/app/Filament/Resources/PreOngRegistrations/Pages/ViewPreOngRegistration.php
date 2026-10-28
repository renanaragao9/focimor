<?php

namespace App\Filament\Resources\PreOngRegistrations\Pages;

use App\Filament\Resources\PreOngRegistrations\PreOngRegistrationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewPreOngRegistration extends ViewRecord
{
    protected static string $resource = PreOngRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->icon(Heroicon::OutlinedPencil),
            DeleteAction::make()->icon(Heroicon::OutlinedTrash),
        ];
    }
}
