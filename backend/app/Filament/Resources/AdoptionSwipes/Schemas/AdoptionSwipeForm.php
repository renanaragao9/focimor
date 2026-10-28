<?php

namespace App\Filament\Resources\AdoptionSwipes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class AdoptionSwipeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Dados da curtida de adoção')
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

                        Select::make('action')
                            ->label('Ação')
                            ->options(['like' => 'Like', 'dislike' => 'Dislike', 'superlike' => 'Superlike'])
                            ->required()
                            ->columnSpanFull(),
                    ]),

            ]);
    }
}
