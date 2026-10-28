<?php

namespace App\Filament\Resources\OngCampaigns\Pages;

use App\Filament\Resources\OngCampaigns\OngCampaignResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewOngCampaign extends ViewRecord
{
    protected static string $resource = OngCampaignResource::class;

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
