<?php

namespace App\Filament\Resources\OngCampaigns;

use App\Filament\Resources\OngCampaigns\Pages\CreateOngCampaign;
use App\Filament\Resources\OngCampaigns\Pages\EditOngCampaign;
use App\Filament\Resources\OngCampaigns\Pages\ListOngCampaigns;
use App\Filament\Resources\OngCampaigns\Pages\ViewOngCampaign;
use App\Filament\Resources\OngCampaigns\Schemas\OngCampaignForm;
use App\Filament\Resources\OngCampaigns\Schemas\OngCampaignInfolist;
use App\Filament\Resources\OngCampaigns\Tables\OngCampaignsTable;
use App\Models\OngCampaign;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OngCampaignResource extends Resource
{
    protected static ?string $model = OngCampaign::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $modelLabel = 'Caixinha';

    protected static ?string $pluralModelLabel = 'Caixinhas';

    protected static ?string $navigationLabel = 'Caixinhas';

    protected static string|UnitEnum|null $navigationGroup = 'ONGs';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return OngCampaignForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OngCampaignInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OngCampaignsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOngCampaigns::route('/'),
            'create' => CreateOngCampaign::route('/create'),
            'view' => ViewOngCampaign::route('/{record}'),
            'edit' => EditOngCampaign::route('/{record}/edit'),
        ];
    }
}
