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
                ->options(\App\Models\Menu::pluck('namaMenu', 'idMenu'))
                ->required()
                ->searchable()
                ->preload(),

            Select::make('idPcs')
                ->label('PCS Tahu')
                ->options(\App\Models\PcsTahu::pluck('namaPcs', 'idPcs'))
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