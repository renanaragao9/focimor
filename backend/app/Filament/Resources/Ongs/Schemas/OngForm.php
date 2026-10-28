<?php

namespace App\Filament\Resources\Ongs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class OngForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Dados da ONG')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->directory('ong-logos')
                            ->disk(config('filesystems.media'))
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, callable $set) => $operation === 'create'
                                ? $set('slug', Str::slug((string) $state))
                                : null),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('cnpj')
                            ->label('CNPJ')
                            ->maxLength(18)
                            ->unique(ignoreRecord: true),

                        Select::make('status')
                            ->label('Status')
                            ->options(['pending' => 'Pendente', 'verified' => 'Verificada', 'suspended' => 'Suspensa'])
                            ->default('pending')
                            ->required(),

                        DateTimePicker::make('verified_at')
                            ->label('Verificada em')
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i'),

                        Textarea::make('description')
                            ->label('Descrição')
                            ->rows(4)
                            ->maxLength(5000)
                            ->columnSpanFull(),
                    ]),

                Section::make('Contato e Localização')
                    ->columnSpanFull()
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('email')->label('E-mail')->email()->maxLength(255),
                        TextInput::make('phone')->label('Telefone')->tel()->maxLength(255),
                        TextInput::make('address')->label('Endereço')->maxLength(255)->columnSpanFull(),
                        TextInput::make('city')->label('Cidade')->maxLength(255),
                        TextInput::make('state')->label('UF')->maxLength(2),
                        TextInput::make('latitude')->label('Latitude')->numeric()->minValue(-90)->maxValue(90),
                        TextInput::make('longitude')->label('Longitude')->numeric()->minValue(-180)->maxValue(180),
                    ]),

                Section::make('Doações')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('pix_key')->label('Chave PIX')->maxLength(255),
                        Textarea::make('donation_info')->label('Como ajudar')->rows(3)->maxLength(5000)->columnSpanFull(),
                    ]),

            ]);
    }
}
