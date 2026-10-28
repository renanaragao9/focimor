<?php

namespace App\Filament\Resources\AdoptionSwipes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdoptionSwipeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Dados da curtida de adoção')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Adotante'),
                        TextEntry::make('pet.name')
                            ->label('Pet'),
                        TextEntry::make('action')
                            ->label('Ação')
                            ->formatStateUsing(fn (string $state) => ['like' => 'Like', 'dislike' => 'Dislike', 'superlike' => 'Superlike'][$state] ?? $state),

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
