<?php

namespace App\Filament\Resources\PreOngRegistrations\Pages;

use App\Filament\Resources\PreOngRegistrations\PreOngRegistrationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditPreOngRegistration extends EditRecord
{
    protected static string $resource = PreOngRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->icon(Heroicon::OutlinedEye),
            DeleteAction::make()->icon(Heroicon::OutlinedTrash),
        ];
    }
}
