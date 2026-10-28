<?php

namespace App\Filament\Resources\Swipes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SwipesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('swiperPet.name')
                    ->label('Pet que curtiu')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('targetPet.name')
                    ->label('Pet alvo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('action')
                    ->label('Ação')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'like' => 'Like',
                        'dislike' => 'Dislike',
                        'superlike' => 'Superlike',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'like' => 'success',
                        'dislike' => 'danger',
                        'superlike' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Ação')
                    ->options([
                        'like' => 'Like',
                        'dislike' => 'Dislike',
                        'superlike' => 'Superlike',
                    ]),
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
