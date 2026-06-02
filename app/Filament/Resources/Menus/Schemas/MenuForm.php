<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('namaMenu')
                ->label('Nama Menu')
                ->required()
                ->maxLength(255),

            TextInput::make('tagline')
                ->label('Tagline')
                ->maxLength(255),

            Textarea::make('deskripsi')
                ->label('Deskripsi')
                ->rows(3),

            FileUpload::make('foto')
                ->label('Foto Menu')
                ->image()
                ->directory('menus')
                ->disk('public'),

            Repeater::make('compositions')
                ->label('Komposisi Menu / Pengurangan Stok')
                ->relationship('compositions')
                ->schema([
                    Select::make('pcs_tahu_id')
                        ->label('Jenis Tahu')
                        ->relationship('pcsTahu', 'nama_pcs')
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('jumlah_pakai')
                        ->label('Jumlah Pakai')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required(),

                ])
                ->columns(2)
                ->defaultItems(1)
                ->addActionLabel('Tambah Komposisi')
                ->reorderable(false)
                ->collapsible()
                ->defaultItems(1),
        ]);
    }
}
