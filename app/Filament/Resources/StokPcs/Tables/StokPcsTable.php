<?php

namespace App\Filament\Resources\StokPcs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StokPcsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cabang.namaCabang')
                    ->label('Cabang')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('pcsTahu.nama_pcs')
                    ->label('PCS Tahu')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah_stok')
                    ->label('Jumlah Stok')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}