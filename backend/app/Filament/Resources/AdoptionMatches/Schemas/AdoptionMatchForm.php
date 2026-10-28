<?php

namespace App\Filament\Resources\AdoptionMatches\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class AdoptionMatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Dados do Match de adoção')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label('Adotante')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule, callable $get) => $rule->where('pet_id', $get('pet_id')),
                            ),

                        Select::make('pet_id')
                            ->label('Pet')
                            ->relationship('pet', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pendente',
                                'accepted' => 'Aceito',
                                'declined' => 'Recusado',
                                'adopted' => 'Adotado',
                                'cancelled' => 'Cancelado',
                            ])
                            ->default('pending')
                            ->required(),

                        DateTimePicker::make('decided_at')
                            ->label('Decidido em')
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i'),

                        Select::make('decided_by_user_id')
                            ->label('Decidido por')
                            ->relationship('decidedBy', 'name')
                            ->searchable()
                            ->preload(),
                    ]),

            ]);
    }
}
