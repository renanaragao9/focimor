<?php

namespace App\Filament\Resources\Ongs\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OngInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Dados da ONG')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        ImageEntry::make('logo_path')->label('Logo')->disk(config('filesystems.media')),
                        TextEntry::make('name')
                            ->label('Nome'),
                        TextEntry::make('slug')
                            ->label('Slug'),
                        TextEntry::make('cnpj')
                            ->label('CNPJ')
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => ['pending' => 'Pendente', 'verified' => 'Verificada', 'suspended' => 'Suspensa'][$state] ?? $state),
                        TextEntry::make('verified_at')
                            ->label('Verificada em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->label('Descrição')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('created_at')
                            ->label('Criado em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Atualizado em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ]),

                Section::make('Contato e Localização')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        TextEntry::make('email')
                            ->label('E-mail')
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->label('Telefone')
                            ->placeholder('-'),
                        TextEntry::make('address')
                            ->label('Endereço')
                            ->placeholder('-'),
                        TextEntry::make('city')
                            ->label('Cidade')
                            ->placeholder('-'),
                        TextEntry::make('state')
                            ->label('UF')
                            ->placeholder('-'),
                        TextEntry::make('latitude')
                            ->label('Latitude')
                            ->placeholder('-'),
                        TextEntry::make('longitude')
                            ->label('Longitude')
                            ->placeholder('-'),

                    ]),

                Section::make('Doações')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        TextEntry::make('pix_key')
                            ->label('Chave PIX')
                            ->placeholder('-'),
                        TextEntry::make('donation_info')
                            ->label('Como ajudar')
                            ->placeholder('-')
                            ->columnSpanFull(),

                    ]),

            ]);
    }
}
