<?php

namespace App\Filament\Resources\Pets\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VaccinesRelationManager extends RelationManager
{
    protected static string $relationship = 'vaccines';

    protected static ?string $title = 'Vacinas';

    protected static ?string $modelLabel = 'Vacina';

    protected static ?string $pluralModelLabel = 'Vacinas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')->label('Vacina')->required()->maxLength(255),
                DatePicker::make('applied_at')->label('Aplicada em')->displayFormat('d/m/Y')->maxDate(now())->required(),
                DatePicker::make('next_dose_at')->label('Próxima dose')->displayFormat('d/m/Y')->after('applied_at'),
                TextInput::make('batch')->label('Lote')->maxLength(255),
                TextInput::make('veterinarian')->label('Veterinário')->maxLength(255),
                Textarea::make('notes')->label('Observações')->rows(2)->columnSpanFull()->maxLength(2000),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Vacina')->searchable(),
                TextColumn::make('applied_at')->label('Aplicada em')->date('d/m/Y')->sortable(),
                TextColumn::make('next_dose_at')->label('Próxima dose')->date('d/m/Y')->sortable()->placeholder('-')
                    ->color(fn ($record) => $record->next_dose_at?->isPast() ? 'danger' : null),
                TextColumn::make('veterinarian')->label('Veterinário')->placeholder('-')->toggleable(),
                TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Atualizado em')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('applied_at', 'desc')
            ->headerActions([
                CreateAction::make()->icon(Heroicon::OutlinedPlus),
            ])
            ->recordActions([
                EditAction::make()->icon(Heroicon::OutlinedPencil),
                DeleteAction::make()->icon(Heroicon::OutlinedTrash),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
