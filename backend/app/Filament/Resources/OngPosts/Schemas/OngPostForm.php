<?php

namespace App\Filament\Resources\OngPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OngPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Dados do Post')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('ong_id')
                            ->label('ONG (vazio = post do sistema)')
                            ->relationship('ong', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('pet_id')
                            ->label('Pet (opcional)')
                            ->relationship('pet', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('body')
                            ->label('Conteúdo')
                            ->rows(8)
                            ->required()
                            ->maxLength(20000)
                            ->columnSpanFull(),

                        FileUpload::make('cover_path')
                            ->label('Capa')
                            ->image()
                            ->directory('ong-posts')
                            ->disk(config('filesystems.media'))
                            ->columnSpanFull(),

                        Select::make('author_user_id')
                            ->label('Autor')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->id()),

                        DateTimePicker::make('published_at')
                            ->label('Publicado em (vazio = rascunho)')
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i'),
                    ]),

            ]);
    }
}
