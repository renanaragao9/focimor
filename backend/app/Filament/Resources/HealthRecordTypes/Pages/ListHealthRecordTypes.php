<?php

namespace App\Filament\Resources\HealthRecordTypes\Pages;

use App\Filament\Resources\HealthRecordTypes\HealthRecordTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListHealthRecordTypes extends ListRecords
{
    protected static string $resource = HealthRecordTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::OutlinedPlus),
        ];
    }
}
