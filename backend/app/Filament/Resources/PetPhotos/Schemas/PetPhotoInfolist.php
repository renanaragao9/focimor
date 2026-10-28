<?php

namespace App\Filament\Resources\PetPhotos\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PetPhotoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Dados da Foto')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        ImageEntry::make('path')
                            ->label('Imagem')
                            ->disk(config('filesystems.media'))
                            ->columnSpanFull(),

                        TextEntry::make('pet.name')
                            ->label('Pet'),

                        TextEntry::make('order')
                            ->label('Ordem'),

                        IconEntry::make('is_primary')
                            ->label('Principal')
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
