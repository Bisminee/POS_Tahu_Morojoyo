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

                TextColumn::make('metodePayment')
                    ->label('Metode Payment')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'takeAwayCash' => 'Take Away Cash',
                        'takeAwayQris' => 'Take Away QRIS',
                        'shopeefood' => 'ShopeeFood',
                        'gofood' => 'GoFood',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR')
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