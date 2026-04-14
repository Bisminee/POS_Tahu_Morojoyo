<?php

namespace App\Filament\Resources\StokPcs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StokPcsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('idCabang')
                ->label('Cabang')
                ->options(\App\Models\Cabang::pluck('Alamat', 'idCabang'))
                ->required()
                ->searchable()
                ->preload(),

            Select::make('idPcs')
                ->label('PCS Tahu')
                ->options(\App\Models\PcsTahu::pluck('namaPcs', 'idPcs'))
                ->required()
                ->searchable()
                ->preload(),

            TextInput::make('jumlahStok')
                ->label('Jumlah Stok')
                ->numeric()
                ->required(),
        ]);
    }
}