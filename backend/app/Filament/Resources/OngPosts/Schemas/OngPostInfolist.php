<?php

namespace App\Filament\Resources\OngPosts\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OngPostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Dados do Post')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        ImageEntry::make('cover_path')->label('Capa')->disk(config('filesystems.media'))->columnSpanFull(),
                        TextEntry::make('title')
                            ->label('Título'),
                        TextEntry::make('ong.name')
                            ->label('ONG')
                            ->placeholder('Sistema'),
                        TextEntry::make('pet.name')
                            ->label('Pet')
                            ->placeholder('-'),
                        TextEntry::make('author.name')
                            ->label('Autor')
                            ->placeholder('-'),
                        TextEntry::make('published_at')
                            ->label('Publicado em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Rascunho'),
                        TextEntry::make('body')
                            ->label('Conteúdo')
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

            ]);
    }
}
