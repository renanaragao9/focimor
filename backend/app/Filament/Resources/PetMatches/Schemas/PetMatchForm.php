<?php

namespace App\Filament\Resources\PetMatches\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class PetMatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Dados do Match')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('pet_one_id')
                            ->label('Pet 1')
                            ->relationship('petOne', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->different('pet_two_id')
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule, callable $get) => $rule->where('pet_two_id', $get('pet_two_id')),
                            ),

                        Select::make('pet_two_id')
                            ->label('Pet 2')
                            ->relationship('petTwo', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->different('pet_one_id'),

                        DateTimePicker::make('matched_at')
                            ->label('Data do match')
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i')
                            ->default(now())
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),
                    ]),
            ]);
    }
}
