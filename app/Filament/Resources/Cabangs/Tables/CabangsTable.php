<?php

namespace App\Filament\Resources\Cabangs\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CabangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('namaCabang')
                    ->label('Nama Cabang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->formatStateUsing(fn ($state): string => $state === false ? 'Nonaktif' : 'Aktif')
                    ->badge()
                    ->color(fn ($state): string => $state === false ? 'danger' : 'success')
                    ->sortable(),

                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->limit(60)
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('nonaktifkan')
                    ->label('Nonaktifkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => $record->is_active !== false)
                    ->action(fn ($record) => $record->update(['is_active' => false])),

                Action::make('aktifkan')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => $record->is_active === false)
                    ->action(fn ($record) => $record->update(['is_active' => true])),
            ])
            ->toolbarActions([]);
    }
}