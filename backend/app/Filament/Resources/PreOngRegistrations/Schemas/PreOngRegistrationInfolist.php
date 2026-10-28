<?php

namespace App\Filament\Resources\PreOngRegistrations\Schemas;

use App\Models\PreOngRegistration;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PreOngRegistrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Pré-cadastro')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        TextEntry::make('name')->label('Nome da ONG'),
                        TextEntry::make('cnpj')->label('CNPJ')->placeholder('-'),
                        TextEntry::make('email')->label('E-mail'),
                        TextEntry::make('phone')->label('Telefone'),
                        TextEntry::make('city')->label('Cidade'),
                        TextEntry::make('state')->label('UF'),
                        TextEntry::make('description')->label('Sobre a ONG')->placeholder('-')->columnSpanFull(),
                        TextEntry::make('status')
                            ->label('Situação')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => PreOngRegistration::STATUS_LABELS[$state] ?? $state),
                        TextEntry::make('ong.name')->label('ONG criada')->placeholder('-'),
                        TextEntry::make('notes')->label('Anotações internas')->placeholder('-')->columnSpanFull(),
                        TextEntry::make('created_at')->label('Enviado em')->dateTime('d/m/Y H:i')->placeholder('-'),
                        TextEntry::make('updated_at')->label('Atualizado em')->dateTime('d/m/Y H:i')->placeholder('-'),
                    ]),
            ]);
    }
}
