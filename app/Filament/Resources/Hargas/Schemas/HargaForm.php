<?php

namespace App\Filament\Resources\Hargas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HargaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('idMenu')
                ->label('Menu')
                ->relationship('menu', 'namaMenu')
                ->required()
                ->searchable()
                ->preload(),

            Select::make('metodePayment')
                ->label('Metode Payment')
                ->options([
                    'takeAwayCash' => 'Take Away Cash',
                    'takeAwayQris' => 'Take Away QRIS',
                    'shopeefood' => 'ShopeeFood',
                    'gofood' => 'GoFood',
                ])
                ->required(),

            TextInput::make('harga')
                ->label('Harga')
                ->numeric()
                ->required(),
        ]);
    }
}