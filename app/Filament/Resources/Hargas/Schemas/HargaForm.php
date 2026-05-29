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

            TextInput::make('harga_normal')
                ->label('Harga Normal')
                ->numeric()
                ->required()
                ->prefix('Rp'),

            TextInput::make('harga_gofood')
                ->label('Harga GoFood')
                ->numeric()
                ->required()
                ->prefix('Rp'),

            TextInput::make('harga_shopeefood')
                ->label('Harga ShopeeFood')
                ->numeric()
                ->required()
                ->prefix('Rp'),
        ]);
    }
}