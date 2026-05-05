<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Repeater;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('namaMenu')
                ->label('Nama Menu')
                ->required()
                ->maxLength(255),

            Repeater::make('compositions')
                ->label('Komposisi Menu / Pengurangan Stok')
                ->relationship('compositions')
                ->schema([
                    Select::make('pcs_tahu_id')
                        ->label('Jenis Tahu')
                ->required(),

            // ✅ FIX #3: Hapus Textarea 'deskripsi' dari form
            // Deskripsi adalah computed/accessor — tidak boleh jadi input.
            // Jika ingin ditampilkan di form (readonly), gunakan Placeholder:
            Placeholder::make('deskripsi')
                ->label('Isi Menu (otomatis)')
                ->content(fn($record) => $record?->deskripsi ?? '—')
                ->visibleOn('edit'), // hanya tampil saat edit, bukan create

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
                        ->minValue(1)
                        ->required(),
                ])
                ->columns(2)
                ->defaultItems(1)
                ->addActionLabel('Tambah Komposisi')
                ->reorderable(false)
                ->collapsible(),
                ->defaultItems(1),
        ]);
    }
}
