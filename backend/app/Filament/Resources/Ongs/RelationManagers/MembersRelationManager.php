<?php

namespace App\Filament\Resources\Ongs\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Membros';

    protected static ?string $modelLabel = 'Membro';

    protected static ?string $pluralModelLabel = 'Membros';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('role')
                ->label('Papel')
                ->options(['admin' => 'Administrador', 'member' => 'Membro'])
                ->default('member')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Nome')->searchable(),
                TextColumn::make('email')->label('E-mail')->searchable(),
                TextColumn::make('role')
                    ->label('Papel')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ['admin' => 'Administrador', 'member' => 'Membro'][$state] ?? $state),
            ])
            ->headerActions([
                AttachAction::make()
                    ->icon(Heroicon::OutlinedPlus)
                    ->preloadRecordSelect()
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('role')
                            ->label('Papel')
                            ->options(['admin' => 'Administrador', 'member' => 'Membro'])
                            ->default('member')
                            ->required(),
                    ]),
            ])
            ->recordActions([
                EditAction::make()->icon(Heroicon::OutlinedPencil),
                DetachAction::make()->icon(Heroicon::OutlinedTrash),
            ]);
    }
}
