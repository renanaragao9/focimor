<?php

namespace App\Filament\Resources\Ongs;

use App\Filament\Resources\Ongs\Pages\CreateOng;
use App\Filament\Resources\Ongs\Pages\EditOng;
use App\Filament\Resources\Ongs\Pages\ListOngs;
use App\Filament\Resources\Ongs\Pages\ViewOng;
use App\Filament\Resources\Ongs\RelationManagers\MembersRelationManager;
use App\Filament\Resources\Ongs\Schemas\OngForm;
use App\Filament\Resources\Ongs\Schemas\OngInfolist;
use App\Filament\Resources\Ongs\Tables\OngsTable;
use App\Models\Ong;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OngResource extends Resource
{
    protected static ?string $model = Ong::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static ?string $modelLabel = 'ONG';

    protected static ?string $pluralModelLabel = 'ONGs';

    protected static ?string $navigationLabel = 'ONGs';

    protected static string|UnitEnum|null $navigationGroup = 'ONGs';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return OngForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OngInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OngsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            MembersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOngs::route('/'),
            'create' => CreateOng::route('/create'),
            'view' => ViewOng::route('/{record}'),
            'edit' => EditOng::route('/{record}/edit'),
        ];
    }
}
