<?php

namespace App\Filament\Resources\Swipes;

use App\Filament\Resources\Swipes\Pages\CreateSwipe;
use App\Filament\Resources\Swipes\Pages\EditSwipe;
use App\Filament\Resources\Swipes\Pages\ListSwipes;
use App\Filament\Resources\Swipes\Pages\ViewSwipe;
use App\Filament\Resources\Swipes\Schemas\SwipeForm;
use App\Filament\Resources\Swipes\Schemas\SwipeInfolist;
use App\Filament\Resources\Swipes\Tables\SwipesTable;
use App\Models\Swipe;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SwipeResource extends Resource
{
    protected static ?string $model = Swipe::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHandRaised;

    protected static ?string $modelLabel = 'Curtida';

    protected static ?string $pluralModelLabel = 'Curtidas';

    protected static ?string $navigationLabel = 'Curtidas';

    protected static string|UnitEnum|null $navigationGroup = 'Interações';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return SwipeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SwipeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SwipesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSwipes::route('/'),
            'create' => CreateSwipe::route('/create'),
            'view' => ViewSwipe::route('/{record}'),
            'edit' => EditSwipe::route('/{record}/edit'),
        ];
    }
}
