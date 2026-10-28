<?php

namespace App\Filament\Resources\PreOngRegistrations\Schemas;

use App\Models\PreOngRegistration;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PreOngRegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Pré-cadastro')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Nome da ONG')->required()->maxLength(255),
                        TextInput::make('cnpj')->label('CNPJ')->maxLength(18),
                        TextInput::make('email')->label('E-mail')->email()->required()->maxLength(255),
                        TextInput::make('phone')->label('Telefone')->required()->maxLength(20),
                        TextInput::make('city')->label('Cidade')->required()->maxLength(255),
                        TextInput::make('state')->label('UF')->required()->length(2),
                        Textarea::make('description')->label('Sobre a ONG')->rows(4)->maxLength(1000)->columnSpanFull(),
                    ]),
                Section::make('Análise')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Situação')
                            ->options(PreOngRegistration::STATUS_LABELS)
                            ->required(),
                        Select::make('ong_id')
                            ->label('ONG criada')
                            ->relationship('ong', 'name')
                            ->searchable()
                            ->preload(),
                        Textarea::make('notes')->label('Anotações internas')->rows(4)->columnSpanFull(),
                    ]),
            ]);
    }
}
