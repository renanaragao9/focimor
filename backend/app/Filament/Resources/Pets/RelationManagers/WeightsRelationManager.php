<?php

namespace App\Filament\Resources\Pets\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WeightsRelationManager extends RelationManager
{
    protected static string $relationship = 'weights';

    protected static ?string $title = 'Pesos';

    protected static ?string $modelLabel = 'Peso';

    protected static ?string $pluralModelLabel = 'Pesos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('height_cm')->label('Altura (cm)')->numeric()->minValue(0.01),
                TextInput::make('width_cm')->label('Largura (cm)')->numeric()->minValue(0.01),
                TextInput::make('weight_kg')->label('Peso (kg)')->numeric()->minValue(0.01)->required(),
                DatePicker::make('measured_at')->label('Medido em')->displayFormat('d/m/Y')->maxDate(now())->default(now())->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('weight_kg')
            ->columns([
                TextColumn::make('weight_kg')->label('Peso (kg)')->sortable(),
                TextColumn::make('height_cm')->label('Altura (cm)')->placeholder('-'),
                TextColumn::make('width_cm')->label('Largura (cm)')->placeholder('-'),
                TextColumn::make('measured_at')->label('Medido em')->date('d/m/Y')->sortable(),
                TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Atualizado em')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('measured_at', 'desc')
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
