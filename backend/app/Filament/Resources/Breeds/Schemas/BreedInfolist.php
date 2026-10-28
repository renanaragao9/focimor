<?php

namespace App\Filament\Resources\Breeds\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BreedInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Dados da Raça')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        TextEntry::make('species.name')
                            ->label('Espécie'),

                        TextEntry::make('name')
                            ->label('Nome'),

                        TextEntry::make('slug')
                            ->label('Slug'),

                        TextEntry::make('order')
                            ->label('Ordem'),

                        IconEntry::make('is_active')
                            ->label('Ativo')
                            ->boolean(),

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
