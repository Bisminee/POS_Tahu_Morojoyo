<?php

namespace App\Filament\Resources\MenuDetails\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MenuDetailForm
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

            Select::make('idPcs')
                ->label('PCS Tahu')
                ->relationship('pcsTahu', 'namaPcs')
                ->required()
                ->searchable()
                ->preload(),

            TextInput::make('jumlahPcs')
                ->label('Jumlah PCS')
                ->numeric()
                ->required(),
        ]);
    }
}