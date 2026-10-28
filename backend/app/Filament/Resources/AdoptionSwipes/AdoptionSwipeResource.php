<?php

namespace App\Filament\Resources\AdoptionSwipes;

use App\Filament\Resources\AdoptionSwipes\Pages\CreateAdoptionSwipe;
use App\Filament\Resources\AdoptionSwipes\Pages\EditAdoptionSwipe;
use App\Filament\Resources\AdoptionSwipes\Pages\ListAdoptionSwipes;
use App\Filament\Resources\AdoptionSwipes\Pages\ViewAdoptionSwipe;
use App\Filament\Resources\AdoptionSwipes\Schemas\AdoptionSwipeForm;
use App\Filament\Resources\AdoptionSwipes\Schemas\AdoptionSwipeInfolist;
use App\Filament\Resources\AdoptionSwipes\Tables\AdoptionSwipesTable;
use App\Models\AdoptionSwipe;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AdoptionSwipeResource extends Resource
{
    protected static ?string $model = AdoptionSwipe::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHandRaised;

    protected static ?string $modelLabel = 'Curtida de adoção';

    protected static ?string $pluralModelLabel = 'Curtidas de adoção';

    protected static ?string $navigationLabel = 'Curtidas de adoção';

    protected static string|UnitEnum|null $navigationGroup = 'Adoção';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return AdoptionSwipeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AdoptionSwipeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdoptionSwipesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdoptionSwipes::route('/'),
            'create' => CreateAdoptionSwipe::route('/create'),
            'view' => ViewAdoptionSwipe::route('/{record}'),
            'edit' => EditAdoptionSwipe::route('/{record}/edit'),
        ];
    }
}
