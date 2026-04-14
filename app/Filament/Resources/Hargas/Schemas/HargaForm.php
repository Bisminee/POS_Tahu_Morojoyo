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

            Select::make('metode_payment')
                ->label('Metode Payment')
                ->options([
                    'take_away_cash' => 'Take Away Cash',
                    'take_away_qris' => 'Take Away QRIS',
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