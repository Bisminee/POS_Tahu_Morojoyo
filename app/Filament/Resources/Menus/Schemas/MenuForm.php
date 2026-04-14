<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('namaMenu')
                ->label('Nama Menu')
                ->required(),
            Textarea::make('deskripsi')
                ->label('Deskripsi Menu')
                ->rows(3),
            Repeater::make('menuDetails')
                ->relationship()
                ->label('Detail Menu')
                ->schema([
                    Select::make('id_pcs')
                        ->label('Jenis Barang')
                        ->relationship('pcsTahu', 'nama_pcs')
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('jumlah_pcs')
                        ->label('Jumlah')
                        ->numeric()
                        ->required(),
                ])
                ->columns(2)
                ->defaultItems(1),
        ]);
    }
}
