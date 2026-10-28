<?php

namespace App\Filament\Resources\Pets\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HealthRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'healthRecords';

    protected static ?string $title = 'Histórico de saúde';

    protected static ?string $modelLabel = 'Registro';

    protected static ?string $pluralModelLabel = 'Histórico de saúde';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('health_record_type_id')->label('Tipo')->relationship('type', 'name')->searchable()->preload()->required(),
                TextInput::make('title')->label('Título')->required()->maxLength(255),
                DatePicker::make('performed_at')->label('Realizado em')->displayFormat('d/m/Y')->maxDate(now())->required(),
                DatePicker::make('next_due_at')->label('Próximo retorno/dose')->displayFormat('d/m/Y')->after('performed_at'),
                TextInput::make('veterinarian')->label('Veterinário')->maxLength(255),
                TextInput::make('cost')->label('Custo')->numeric()->prefix('R$')->minValue(0),
                Textarea::make('description')->label('Descrição')->rows(3)->columnSpanFull()->maxLength(5000),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->modifyQueryUsing(fn ($query) => $query->with('type'))
            ->columns([
                TextColumn::make('type.name')->label('Tipo')->badge(),
                TextColumn::make('title')->label('Título')->searchable(),
                TextColumn::make('performed_at')->label('Realizado em')->date('d/m/Y')->sortable(),
                TextColumn::make('next_due_at')->label('Próximo')->date('d/m/Y')->sortable()->placeholder('-'),
                TextColumn::make('cost')->label('Custo')->money('BRL')->placeholder('-')->toggleable(),
                TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Atualizado em')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('performed_at', 'desc')
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
