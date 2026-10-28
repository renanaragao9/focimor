<?php

namespace App\Filament\Resources\Pets;

use App\Filament\Resources\Pets\Pages\CreatePet;
use App\Filament\Resources\Pets\Pages\EditPet;
use App\Filament\Resources\Pets\Pages\ListPets;
use App\Filament\Resources\Pets\Pages\ViewPet;
use App\Filament\Resources\Pets\RelationManagers\HealthRecordsRelationManager;
use App\Filament\Resources\Pets\RelationManagers\PhotosRelationManager;
use App\Filament\Resources\Pets\RelationManagers\VaccinesRelationManager;
use App\Filament\Resources\Pets\RelationManagers\WeightsRelationManager;
use App\Filament\Resources\Pets\Schemas\PetForm;
use App\Filament\Resources\Pets\Schemas\PetInfolist;
use App\Filament\Resources\Pets\Tables\PetsTable;
use App\Models\Pet;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PetResource extends Resource
{
    protected static ?string $model = Pet::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static ?string $modelLabel = 'Pet';

    protected static ?string $pluralModelLabel = 'Pets';

    protected static ?string $navigationLabel = 'Pets';

    protected static string|UnitEnum|null $navigationGroup = 'Cadastros';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return PetForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PhotosRelationManager::class,
            VaccinesRelationManager::class,
            HealthRecordsRelationManager::class,
            WeightsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPets::route('/'),
            'create' => CreatePet::route('/create'),
            'view' => ViewPet::route('/{record}'),
            'edit' => EditPet::route('/{record}/edit'),
        ];
    }
}
