<?php

namespace App\Filament\Resources\PetPhotos\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PetPhotoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Dados da Foto')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('pet_id')
                            ->label('Pet')
                            ->relationship('pet', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        FileUpload::make('path')
                            ->label('Imagem')
                            ->image()
                            ->directory('pet-photos')
                            ->disk(config('filesystems.media'))
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('order')
                            ->label('Ordem')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Toggle::make('is_primary')
                            ->label('Principal')
                            ->default(false),
                    ]),
            ]);
    }
}
