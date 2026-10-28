<?php

namespace App\Filament\Resources\Messages\Tables;

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

class MessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sender.name')
                    ->label('Remetente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('body')
                    ->label('Mensagem')
                    ->limit(60)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('petMatch.uuid')
                    ->label('Match')
                    ->limit(13)
                    ->toggleable(),

                IconColumn::make('read_at')
                    ->label('Lida')
                    ->boolean()
                    ->state(fn ($record) => $record->read_at !== null)
                    ->sortable(),

                TextColumn::make('read_at')
                    ->label('Lida em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Enviada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('sender_user_id')
                    ->label('Remetente')
                    ->relationship('sender', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('read_at')
                    ->label('Lida')
                    ->nullable()
                    ->trueLabel('Lidas')
                    ->falseLabel('Não lidas'),
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
