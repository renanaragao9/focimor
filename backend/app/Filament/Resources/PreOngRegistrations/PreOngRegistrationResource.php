<?php

namespace App\Filament\Resources\PreOngRegistrations;

use App\Filament\Resources\PreOngRegistrations\Pages\EditPreOngRegistration;
use App\Filament\Resources\PreOngRegistrations\Pages\ListPreOngRegistrations;
use App\Filament\Resources\PreOngRegistrations\Pages\ViewPreOngRegistration;
use App\Filament\Resources\PreOngRegistrations\Schemas\PreOngRegistrationForm;
use App\Filament\Resources\PreOngRegistrations\Schemas\PreOngRegistrationInfolist;
use App\Filament\Resources\PreOngRegistrations\Tables\PreOngRegistrationsTable;
use App\Models\PreOngRegistration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PreOngRegistrationResource extends Resource
{
    protected static ?string $model = PreOngRegistration::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $modelLabel = 'Pré-cadastro de ONG';

    protected static ?string $pluralModelLabel = 'Pré-cadastros de ONGs';

    protected static ?string $navigationLabel = 'Pré-cadastros';

    protected static string|UnitEnum|null $navigationGroup = 'ONGs';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        $pending = PreOngRegistration::where('status', 'pending')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return PreOngRegistrationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PreOngRegistrationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PreOngRegistrationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPreOngRegistrations::route('/'),
            'view' => ViewPreOngRegistration::route('/{record}'),
            'edit' => EditPreOngRegistration::route('/{record}/edit'),
        ];
    }
}
