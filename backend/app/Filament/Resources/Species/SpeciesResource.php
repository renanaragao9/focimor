<?php

namespace App\Filament\Resources\Species;

use App\Filament\Resources\Species\Pages\CreateSpecies;
use App\Filament\Resources\Species\Pages\EditSpecies;
use App\Filament\Resources\Species\Pages\ListSpecies;
use App\Filament\Resources\Species\Pages\ViewSpecies;
use App\Filament\Resources\Species\Schemas\SpeciesForm;
use App\Filament\Resources\Species\Schemas\SpeciesInfolist;
use App\Filament\Resources\Species\Tables\SpeciesTable;
use App\Models\Species;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SpeciesResource extends Resource
{
    protected static ?string $model = Species::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $modelLabel = 'Espécie';

    protected static ?string $pluralModelLabel = 'Espécies';

    protected static ?string $navigationLabel = 'Espécies';

    protected static string|UnitEnum|null $navigationGroup = 'Catálogos';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return SpeciesForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SpeciesInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SpeciesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSpecies::route('/'),
            'create' => CreateSpecies::route('/create'),
            'view' => ViewSpecies::route('/{record}'),
            'edit' => EditSpecies::route('/{record}/edit'),
        ];
    }
}
