<?php

namespace App\Filament\Resources\Messages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Dados da Mensagem')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('pet_match_id')
                            ->label('Match')
                            ->relationship('petMatch', 'uuid')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('sender_user_id')
                            ->label('Remetente')
                            ->relationship('sender', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Textarea::make('body')
                            ->label('Mensagem')
                            ->rows(4)
                            ->required()
                            ->maxLength(2000)
                            ->columnSpanFull(),

                        DateTimePicker::make('read_at')
                            ->label('Lida em')
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Deixe em branco se a mensagem ainda não foi lida.'),
                    ]),
            ]);
    }
}
