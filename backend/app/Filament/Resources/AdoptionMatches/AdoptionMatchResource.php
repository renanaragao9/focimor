<?php

namespace App\Filament\Resources\AdoptionMatches;

use App\Filament\Resources\AdoptionMatches\Pages\CreateAdoptionMatch;
use App\Filament\Resources\AdoptionMatches\Pages\EditAdoptionMatch;
use App\Filament\Resources\AdoptionMatches\Pages\ListAdoptionMatches;
use App\Filament\Resources\AdoptionMatches\Pages\ViewAdoptionMatch;
use App\Filament\Resources\AdoptionMatches\RelationManagers\MessagesRelationManager;
use App\Filament\Resources\AdoptionMatches\Schemas\AdoptionMatchForm;
use App\Filament\Resources\AdoptionMatches\Schemas\AdoptionMatchInfolist;
use App\Filament\Resources\AdoptionMatches\Tables\AdoptionMatchesTable;
use App\Models\AdoptionMatch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AdoptionMatchResource extends Resource
{
    protected static ?string $model = AdoptionMatch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static ?string $modelLabel = 'Match de adoção';

    protected static ?string $pluralModelLabel = 'Matches de adoção';

    protected static ?string $navigationLabel = 'Matches de adoção';

    protected static string|UnitEnum|null $navigationGroup = 'Adoção';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return AdoptionMatchForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AdoptionMatchInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdoptionMatchesTable::configure($table);
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
            'index' => ListAdoptionMatches::route('/'),
            'create' => CreateAdoptionMatch::route('/create'),
            'view' => ViewAdoptionMatch::route('/{record}'),
            'edit' => EditAdoptionMatch::route('/{record}/edit'),
        ];
    }
}
