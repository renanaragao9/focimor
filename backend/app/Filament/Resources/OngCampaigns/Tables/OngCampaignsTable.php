<?php

namespace App\Filament\Resources\OngCampaigns\Tables;

use App\Models\OngCampaign;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OngCampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ong.name')
                    ->label('ONG')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('goal_amount')
                    ->label('Meta')
                    ->money('BRL')
                    ->sortable(),
                TextColumn::make('raised_amount')
                    ->label('Arrecadado')
                    ->money('BRL')
                    ->sortable(),
                TextColumn::make('progress')
                    ->label('Progresso')
                    ->state(fn (OngCampaign $record) => $record->progressPercent().'%'),
                TextColumn::make('status')
                    ->label('Situação')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => OngCampaign::STATUS_LABELS[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Encerra em')
                    ->date('d/m/Y')
                    ->placeholder('Sem prazo')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Situação')
                    ->options(OngCampaign::STATUS_LABELS),
                SelectFilter::make('ong_id')
                    ->label('ONG')
                    ->relationship('ong', 'name'),
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
