<?php

namespace App\Filament\Resources\HealthRecordTypes\Pages;

use App\Filament\Resources\HealthRecordTypes\HealthRecordTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewHealthRecordType extends ViewRecord
{
    protected static string $resource = HealthRecordTypeResource::class;

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
