<?php

namespace App\Filament\Resources\PetMatches;

use App\Filament\Resources\PetMatches\Pages\CreatePetMatch;
use App\Filament\Resources\PetMatches\Pages\EditPetMatch;
use App\Filament\Resources\PetMatches\Pages\ListPetMatches;
use App\Filament\Resources\PetMatches\Pages\ViewPetMatch;
use App\Filament\Resources\PetMatches\RelationManagers\MessagesRelationManager;
use App\Filament\Resources\PetMatches\Schemas\PetMatchForm;
use App\Filament\Resources\PetMatches\Schemas\PetMatchInfolist;
use App\Filament\Resources\PetMatches\Tables\PetMatchesTable;
use App\Models\PetMatch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PetMatchResource extends Resource
{
    protected static ?string $model = PetMatch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $modelLabel = 'Match';

    protected static ?string $pluralModelLabel = 'Matches';

    protected static ?string $navigationLabel = 'Matches';

    protected static string|UnitEnum|null $navigationGroup = 'Interações';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PetMatchForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PetMatchInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PetMatchesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPetMatches::route('/'),
            'create' => CreatePetMatch::route('/create'),
            'view' => ViewPetMatch::route('/{record}'),
            'edit' => EditPetMatch::route('/{record}/edit'),
        ];
    }
}
