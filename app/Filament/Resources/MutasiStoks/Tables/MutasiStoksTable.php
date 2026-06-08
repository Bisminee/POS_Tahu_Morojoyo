<?php

namespace App\Filament\Resources\MutasiStoks\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class MutasiStoksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('cabang.namaCabang')
                    ->label('Cabang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pcsTahu.nama_pcs')
                    ->label('PCS Tahu')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tipe')
                    ->label('Tipe Mutasi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'masuk' => 'success',
                        'keluar' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('stok_sebelum')
                    ->label('Stok Sebelum')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('stok_sesudah')
                    ->label('Stok Sesudah')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->wrap(),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('id_cabang')
                    ->label('Cabang')
                    ->relationship('cabang', 'namaCabang'),

                SelectFilter::make('id_pcs_tahu')
                    ->label('PCS Tahu')
                    ->relationship('pcsTahu', 'nama_pcs'),

                SelectFilter::make('tipe')
                    ->label('Tipe Mutasi')
                    ->options([
                        'masuk' => 'Masuk',
                        'keluar' => 'Keluar',
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}