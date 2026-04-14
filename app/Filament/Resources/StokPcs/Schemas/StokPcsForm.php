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
            Select::make('id_cabang')
                ->label('Cabang')
                ->relationship('cabang', 'Alamat')
                ->required()
                ->searchable()
                ->preload(),

            Select::make('id_pcs_tahu')
                ->label('PCS Tahu')
                ->relationship('pcsTahu', 'nama_pcs')
                ->required()
                ->searchable()
                ->preload(),

            TextInput::make('jumlah_stok')
                ->label('Jumlah Stok')
                ->numeric()
                ->required(),
        ]);
    }
}