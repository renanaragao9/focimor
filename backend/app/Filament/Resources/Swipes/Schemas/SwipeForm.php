<?php

namespace App\Filament\Resources\Swipes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class SwipeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Dados da curtida')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('swiper_pet_id')
                            ->label('Pet que curtiu')
                            ->relationship('swiperPet', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->different('target_pet_id')
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule, callable $get) => $rule->where('target_pet_id', $get('target_pet_id')),
                            ),

                        Select::make('target_pet_id')
                            ->label('Pet alvo')
                            ->relationship('targetPet', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->different('swiper_pet_id'),

                        Select::make('action')
                            ->label('Ação')
                            ->options([
                                'like' => 'Like',
                                'dislike' => 'Dislike',
                                'superlike' => 'Superlike',
                            ])
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
