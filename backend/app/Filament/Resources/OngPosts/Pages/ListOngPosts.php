<?php

namespace App\Filament\Resources\OngPosts\Pages;

use App\Filament\Resources\OngPosts\OngPostResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListOngPosts extends ListRecords
{
    protected static string $resource = OngPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::OutlinedPlus),
        ];
    }
}
