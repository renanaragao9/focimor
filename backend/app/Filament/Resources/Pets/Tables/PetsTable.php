<?php

namespace App\Filament\Resources\Pets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('gender')
                    ->label('Sexo')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'male' ? 'Macho' : 'Fêmea')
                    ->color(fn (string $state) => $state === 'male' ? 'info' : 'danger'),

                TextColumn::make('user.name')
                    ->label('Dono')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('species.name')
                    ->label('Espécie')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('breed.name')
                    ->label('Raça')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('goal.name')
                    ->label('Objetivo')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('species_id')
                    ->label('Espécie')
                    ->relationship('species', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('goal_id')
                    ->label('Objetivo')
                    ->relationship('goal', 'name')
                    ->preload(),

                SelectFilter::make('gender')
                    ->label('Sexo')
                    ->options([
                        'male' => 'Macho',
                        'female' => 'Fêmea',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Ativo'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->icon(Heroicon::OutlinedEye),
                EditAction::make()
                    ->icon(Heroicon::OutlinedPencil),
                DeleteAction::make()
                    ->icon(Heroicon::OutlinedTrash),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
