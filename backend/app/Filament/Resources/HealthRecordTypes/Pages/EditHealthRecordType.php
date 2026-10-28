<?php

namespace App\Filament\Resources\HealthRecordTypes\Pages;

use App\Filament\Resources\HealthRecordTypes\HealthRecordTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditHealthRecordType extends EditRecord
{
    protected static string $resource = HealthRecordTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon(Heroicon::OutlinedEye),
            DeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
        ];
    }
}
