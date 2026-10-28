<?php

namespace App\Filament\Resources\Pets\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Identificação')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')->label('Nome'),
                        TextEntry::make('gender')
                            ->label('Sexo')
                            ->formatStateUsing(fn (string $state) => $state === 'male' ? 'Macho' : 'Fêmea'),
                        TextEntry::make('birth_date')
                            ->label('Data de nascimento')
                            ->date('d/m/Y')
                            ->placeholder('-'),
                        TextEntry::make('uuid')->label('UUID'),
                        TextEntry::make('color')->label('Cor')->placeholder('-'),
                        TextEntry::make('bio')
                            ->label('Bio')
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ]),

                Section::make('Dono e Classificação')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.name')->label('Dono'),
                        TextEntry::make('goal.name')->label('Objetivo'),
                        TextEntry::make('species.name')->label('Espécie'),
                        TextEntry::make('breed.name')->label('Raça')->placeholder('-'),
                        TextEntry::make('size.name')->label('Porte')->placeholder('-'),
                    ]),

                Section::make('Saúde e Status')
                    ->columns(3)
                    ->schema([
                        IconEntry::make('is_neutered')->label('Castrado')->boolean(),
                        IconEntry::make('is_vaccinated')->label('Vacinado')->boolean(),
                        IconEntry::make('is_active')->label('Ativo')->boolean(),
                    ]),

                Section::make('Localização')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextEntry::make('latitude')->label('Latitude')->placeholder('-'),
                        TextEntry::make('longitude')->label('Longitude')->placeholder('-'),
                    ]),

                Section::make('Registro')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Criado em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Atualizado em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
