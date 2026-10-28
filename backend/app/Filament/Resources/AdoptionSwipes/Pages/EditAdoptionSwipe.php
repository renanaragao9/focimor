<?php

namespace App\Filament\Resources\AdoptionSwipes\Pages;

use App\Filament\Resources\AdoptionSwipes\AdoptionSwipeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditAdoptionSwipe extends EditRecord
{
    protected static string $resource = AdoptionSwipeResource::class;

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
