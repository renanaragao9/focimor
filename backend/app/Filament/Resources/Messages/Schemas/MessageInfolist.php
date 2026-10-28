<?php

namespace App\Filament\Resources\Messages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Dados da Mensagem')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        TextEntry::make('sender.name')
                            ->label('Remetente'),

                        TextEntry::make('petMatch.uuid')
                            ->label('Match'),

                        TextEntry::make('body')
                            ->label('Mensagem')
                            ->columnSpanFull(),

                        TextEntry::make('uuid')
                            ->label('UUID'),

                        TextEntry::make('read_at')
                            ->label('Lida em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Não lida'),

                        TextEntry::make('created_at')
                            ->label('Enviada em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Atualizada em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
