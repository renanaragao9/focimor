<?php

namespace App\Filament\Resources\PetMatches\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PetMatchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Dados do Match')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        TextEntry::make('petOne.name')
                            ->label('Pet 1'),

                        TextEntry::make('petTwo.name')
                            ->label('Pet 2'),

                        TextEntry::make('uuid')
                            ->label('UUID'),

                        IconEntry::make('is_active')
                            ->label('Ativo')
                            ->boolean(),

                        TextEntry::make('matched_at')
                            ->label('Data do match')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),

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
