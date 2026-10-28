<?php

namespace App\Filament\Resources\AdoptionMatches\Pages;

use App\Filament\Resources\AdoptionMatches\AdoptionMatchResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditAdoptionMatch extends EditRecord
{
    protected static string $resource = AdoptionMatchResource::class;

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
