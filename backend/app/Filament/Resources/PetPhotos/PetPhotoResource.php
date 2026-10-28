<?php

namespace App\Filament\Resources\PetPhotos;

use App\Filament\Resources\PetPhotos\Pages\CreatePetPhoto;
use App\Filament\Resources\PetPhotos\Pages\EditPetPhoto;
use App\Filament\Resources\PetPhotos\Pages\ListPetPhotos;
use App\Filament\Resources\PetPhotos\Pages\ViewPetPhoto;
use App\Filament\Resources\PetPhotos\Schemas\PetPhotoForm;
use App\Filament\Resources\PetPhotos\Schemas\PetPhotoInfolist;
use App\Filament\Resources\PetPhotos\Tables\PetPhotosTable;
use App\Models\PetPhoto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PetPhotoResource extends Resource
{
    protected static ?string $model = PetPhoto::class;

    protected static ?string $recordTitleAttribute = 'path';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $modelLabel = 'Foto de Pet';

    protected static ?string $pluralModelLabel = 'Fotos de Pets';

    protected static ?string $navigationLabel = 'Fotos de Pets';

    protected static string|UnitEnum|null $navigationGroup = 'Cadastros';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PetPhotoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PetPhotoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PetPhotosTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPetPhotos::route('/'),
            'create' => CreatePetPhoto::route('/create'),
            'view' => ViewPetPhoto::route('/{record}'),
            'edit' => EditPetPhoto::route('/{record}/edit'),
        ];
    }
}
