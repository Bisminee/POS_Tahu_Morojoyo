<?php

namespace App\Filament\Resources\Hargas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HargasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('menu.namaMenu')
                    ->label('Menu')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('harga_normal')
                    ->label('Harga Normal')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('harga_gofood')
                    ->label('Harga GoFood')
                    ->money('IDR')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('harga_shopeefood')
                    ->label('Harga ShopeeFood')
                    ->money('IDR')
                    ->sortable()
                    ->placeholder('-'),

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