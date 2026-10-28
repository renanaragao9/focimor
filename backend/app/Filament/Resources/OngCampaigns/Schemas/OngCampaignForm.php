<?php

namespace App\Filament\Resources\OngCampaigns\Schemas;

use App\Models\OngCampaign;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OngCampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Dados da Caixinha')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('ong_id')
                            ->label('ONG')
                            ->relationship('ong', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('status')
                            ->label('Situação')
                            ->options(OngCampaign::STATUS_LABELS)
                            ->default('active')
                            ->required(),
                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Descrição')
                            ->rows(5)
                            ->maxLength(5000)
                            ->columnSpanFull(),
                        TextInput::make('goal_amount')
                            ->label('Meta (R$)')
                            ->numeric()
                            ->minValue(1)
                            ->prefix('R$')
                            ->required(),
                        TextInput::make('raised_amount')
                            ->label('Arrecadado (R$)')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefix('R$')
                            ->required(),
                        DatePicker::make('ends_at')
                            ->label('Encerra em')
                            ->displayFormat('d/m/Y'),
                    ]),
            ]);
    }
}
