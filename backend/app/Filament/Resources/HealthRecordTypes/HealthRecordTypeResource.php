<?php

namespace App\Filament\Resources\HealthRecordTypes;

use App\Filament\Resources\HealthRecordTypes\Pages\CreateHealthRecordType;
use App\Filament\Resources\HealthRecordTypes\Pages\EditHealthRecordType;
use App\Filament\Resources\HealthRecordTypes\Pages\ListHealthRecordTypes;
use App\Filament\Resources\HealthRecordTypes\Pages\ViewHealthRecordType;
use App\Filament\Resources\HealthRecordTypes\Schemas\HealthRecordTypeForm;
use App\Filament\Resources\HealthRecordTypes\Schemas\HealthRecordTypeInfolist;
use App\Filament\Resources\HealthRecordTypes\Tables\HealthRecordTypesTable;
use App\Models\HealthRecordType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HealthRecordTypeResource extends Resource
{
    protected static ?string $model = HealthRecordType::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $modelLabel = 'Tipo de registro';

    protected static ?string $pluralModelLabel = 'Tipos de registro';

    protected static ?string $navigationLabel = 'Tipos de registro';

    protected static string|UnitEnum|null $navigationGroup = 'Catálogos';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return HealthRecordTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HealthRecordTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HealthRecordTypesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHealthRecordTypes::route('/'),
            'create' => CreateHealthRecordType::route('/create'),
            'view' => ViewHealthRecordType::route('/{record}'),
            'edit' => EditHealthRecordType::route('/{record}/edit'),
        ];
    }
}
