<?php

namespace App\Filament\Resources\OngPosts\Pages;

use App\Filament\Resources\OngPosts\OngPostResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewOngPost extends ViewRecord
{
    protected static string $resource = OngPostResource::class;

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
