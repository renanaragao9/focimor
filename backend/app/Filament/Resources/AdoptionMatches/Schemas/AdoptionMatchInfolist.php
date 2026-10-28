<?php

namespace App\Filament\Resources\AdoptionMatches\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdoptionMatchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Dados do Match de adoção')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Adotante'),
                        TextEntry::make('pet.name')
                            ->label('Pet'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => ['pending' => 'Pendente', 'accepted' => 'Aceito', 'declined' => 'Recusado', 'adopted' => 'Adotado', 'cancelled' => 'Cancelado'][$state] ?? $state),
                        TextEntry::make('decided_at')
                            ->label('Decidido em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('decidedBy.name')
                            ->label('Decidido por')
                            ->placeholder('-'),

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
