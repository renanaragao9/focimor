<?php

namespace App\Filament\Resources\OngCampaigns\Schemas;

use App\Models\OngCampaign;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OngCampaignInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Dados da Caixinha')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        TextEntry::make('title')
                            ->label('Título'),
                        TextEntry::make('ong.name')
                            ->label('ONG'),
                        TextEntry::make('status')
                            ->label('Situação')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => OngCampaign::STATUS_LABELS[$state] ?? $state),
                        TextEntry::make('ends_at')
                            ->label('Encerra em')
                            ->date('d/m/Y')
                            ->placeholder('Sem prazo'),
                        TextEntry::make('goal_amount')
                            ->label('Meta')
                            ->money('BRL'),
                        TextEntry::make('raised_amount')
                            ->label('Arrecadado')
                            ->money('BRL'),
                        TextEntry::make('progress')
                            ->label('Progresso')
                            ->state(fn (OngCampaign $record) => $record->progressPercent().'%'),
                        TextEntry::make('description')
                            ->label('Descrição')
                            ->placeholder('-')
                            ->columnSpanFull(),
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
