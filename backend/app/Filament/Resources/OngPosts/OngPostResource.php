<?php

namespace App\Filament\Resources\OngPosts;

use App\Filament\Resources\OngPosts\Pages\CreateOngPost;
use App\Filament\Resources\OngPosts\Pages\EditOngPost;
use App\Filament\Resources\OngPosts\Pages\ListOngPosts;
use App\Filament\Resources\OngPosts\Pages\ViewOngPost;
use App\Filament\Resources\OngPosts\Schemas\OngPostForm;
use App\Filament\Resources\OngPosts\Schemas\OngPostInfolist;
use App\Filament\Resources\OngPosts\Tables\OngPostsTable;
use App\Models\OngPost;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OngPostResource extends Resource
{
    protected static ?string $model = OngPost::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $modelLabel = 'Post';

    protected static ?string $pluralModelLabel = 'Posts';

    protected static ?string $navigationLabel = 'Posts';

    protected static string|UnitEnum|null $navigationGroup = 'ONGs';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return OngPostForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OngPostInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OngPostsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOngPosts::route('/'),
            'create' => CreateOngPost::route('/create'),
            'view' => ViewOngPost::route('/{record}'),
            'edit' => EditOngPost::route('/{record}/edit'),
        ];
    }
}
