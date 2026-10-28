<?php

namespace App\Filament\Resources\OngCampaigns\Pages;

use App\Filament\Resources\OngCampaigns\OngCampaignResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditOngCampaign extends EditRecord
{
    protected static string $resource = OngCampaignResource::class;

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
