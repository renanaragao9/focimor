<?php

namespace App\Filament\Resources\Pets\Schemas;

use App\Models\Breed;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Identificação')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),

                        Select::make('gender')
                            ->label('Sexo')
                            ->options([
                                'male' => 'Macho',
                                'female' => 'Fêmea',
                            ])
                            ->required(),

                        DatePicker::make('birth_date')
                            ->label('Data de nascimento')
                            ->displayFormat('d/m/Y')
                            ->maxDate(now()),

                        TextInput::make('color')->label('Cor')->maxLength(50),

                        Textarea::make('bio')
                            ->label('Bio')
                            ->rows(3)
                            ->columnSpanFull()
                            ->maxLength(2000),
                    ]),

                Section::make('Dono e Classificação')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label('Dono')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('goal_id')
                            ->label('Objetivo')
                            ->relationship('goal', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('ong_id')
                            ->label('ONG (opcional)')
                            ->relationship('ong', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('species_id')
                            ->label('Espécie')
                            ->relationship('species', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('breed_id', null)),

                        Select::make('breed_id')
                            ->label('Raça')
                            ->options(fn (callable $get) => $get('species_id')
                                ? Breed::query()
                                    ->where('species_id', $get('species_id'))
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all()
                                : [])
                            ->searchable()
                            ->preload()
                            ->placeholder('Selecione a espécie primeiro'),

                        Select::make('size_id')
                            ->label('Porte')
                            ->relationship('size', 'name')
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Saúde e Status')
                    ->columns(3)
                    ->schema([
                        Toggle::make('is_neutered')
                            ->label('Castrado')
                            ->default(false),

                        Toggle::make('is_vaccinated')
                            ->label('Vacinado')
                            ->default(false),

                        Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),
                    ]),

                Section::make('Detalhes do pet')
                    ->relationship('detail')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('microchip')->label('Microchip')->maxLength(255)->unique(ignoreRecord: true),
                        TextInput::make('pedigree_number')->label('Nº do pedigree')->maxLength(255),
                        TextInput::make('coat')->label('Pelagem')->maxLength(255),
                        TextInput::make('blood_type')->label('Tipo sanguíneo')->maxLength(255),
                        TextInput::make('temperament')->label('Temperamento')->maxLength(255),
                        Textarea::make('allergies')->label('Alergias')->rows(2)->maxLength(2000),
                        Textarea::make('chronic_conditions')->label('Condições crônicas')->rows(2)->maxLength(2000),
                        Textarea::make('diet')->label('Alimentação')->rows(2)->columnSpanFull()->maxLength(2000),
                        TextInput::make('vet_name')->label('Veterinário')->maxLength(255),
                        TextInput::make('vet_clinic')->label('Clínica')->maxLength(255),
                        TextInput::make('vet_phone')->label('Telefone da clínica')->tel()->maxLength(255),
                        Textarea::make('notes')->label('Observações')->rows(3)->columnSpanFull()->maxLength(5000),
                    ]),

                Section::make('Localização')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90),

                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180),
                    ]),
            ]);
    }
}
