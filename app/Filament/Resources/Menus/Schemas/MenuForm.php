<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('namaMenu')
                ->label('Nama Menu')
                ->required()
                ->maxLength(255),

            FileUpload::make('foto')
                ->label('Foto Produk')
                ->image()
                ->directory('menus')
                ->disk('public'),

            TextInput::make('tagline_product')
                ->label('Tagline Product')
                ->maxLength(255),

            Textarea::make('deskripsi_produk')
                ->label('Deskripsi Produk')
                ->rows(4),

            Placeholder::make('deskripsi')
                ->label('Isi Menu (otomatis)')
                ->content(fn ($record) => $record?->deskripsi ?? '—')
                ->visibleOn('edit'),


            Repeater::make('compositions')
                ->label('Komposisi Menu')
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
                ->collapsible(),

        ]);
    }
}