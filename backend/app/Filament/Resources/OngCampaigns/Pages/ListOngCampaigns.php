<?php

namespace App\Filament\Resources\OngCampaigns\Pages;

use App\Filament\Resources\OngCampaigns\OngCampaignResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListOngCampaigns extends ListRecords
{
    protected static string $resource = OngCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::OutlinedPlus),
        ];
    }
}
