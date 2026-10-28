<?php

namespace App\Filament\Resources\Breeds;

use App\Filament\Resources\Breeds\Pages\CreateBreed;
use App\Filament\Resources\Breeds\Pages\EditBreed;
use App\Filament\Resources\Breeds\Pages\ListBreeds;
use App\Filament\Resources\Breeds\Pages\ViewBreed;
use App\Filament\Resources\Breeds\Schemas\BreedForm;
use App\Filament\Resources\Breeds\Schemas\BreedInfolist;
use App\Filament\Resources\Breeds\Tables\BreedsTable;
use App\Models\Breed;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BreedResource extends Resource
{
    protected static ?string $model = Breed::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $modelLabel = 'Raça';

    protected static ?string $pluralModelLabel = 'Raças';

    protected static ?string $navigationLabel = 'Raças';

    protected static string|UnitEnum|null $navigationGroup = 'Catálogos';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return BreedForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BreedInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BreedsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBreeds::route('/'),
            'create' => CreateBreed::route('/create'),
            'view' => ViewBreed::route('/{record}'),
            'edit' => EditBreed::route('/{record}/edit'),
        ];
    }
}
