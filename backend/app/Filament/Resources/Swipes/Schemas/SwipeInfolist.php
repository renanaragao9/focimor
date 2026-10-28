<?php

namespace App\Filament\Resources\Swipes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SwipeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Dados da curtida')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        TextEntry::make('swiperPet.name')
                            ->label('Pet que curtiu'),

                        TextEntry::make('targetPet.name')
                            ->label('Pet alvo'),

                        TextEntry::make('action')
                            ->label('Ação')
                            ->formatStateUsing(fn (string $state) => match ($state) {
                                'like' => 'Like',
                                'dislike' => 'Dislike',
                                'superlike' => 'Superlike',
                                default => $state,
                            }),

                        TextEntry::make('created_at')
                            ->label('Criado em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Atualizado em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
