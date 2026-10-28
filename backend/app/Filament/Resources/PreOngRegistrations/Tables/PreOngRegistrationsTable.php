<?php

namespace App\Filament\Resources\PreOngRegistrations\Tables;

use App\Models\PreOngRegistration;
use App\Services\Api\Global\PreOngRegistration\ApprovePreOngRegistrationService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PreOngRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('ONG')->searchable()->sortable(),
                TextColumn::make('email')->label('E-mail')->searchable(),
                TextColumn::make('phone')->label('Telefone')->searchable(),
                TextColumn::make('city')->label('Cidade')->sortable(),
                TextColumn::make('state')->label('UF')->sortable(),
                TextColumn::make('status')
                    ->label('Situação')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => PreOngRegistration::STATUS_LABELS[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('created_at')->label('Enviado em')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Situação')->options(PreOngRegistration::STATUS_LABELS),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('approve')
                    ->label('Aprovar e criar ONG')
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Cria a ONG já verificada. Se existir um usuário com o e-mail do pré-cadastro, ele vira administrador da ONG.')
                    ->visible(fn (PreOngRegistration $record) => $record->ong_id === null && $record->status !== 'rejected')
                    ->action(function (PreOngRegistration $record): void {
                        $ong = app(ApprovePreOngRegistrationService::class)->run($record);

                        Notification::make()->title("ONG {$ong->name} criada")->success()->send();
                    }),
                ViewAction::make()->icon(Heroicon::OutlinedEye),
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
