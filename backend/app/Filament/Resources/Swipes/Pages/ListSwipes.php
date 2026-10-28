<?php

namespace App\Filament\Resources\Swipes\Pages;

use App\Filament\Resources\Swipes\SwipeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListSwipes extends ListRecords
{
    protected static string $resource = SwipeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::OutlinedPlus),
        ];
    }
}
