<?php

namespace App\Filament\Resources\Karyawans\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KaryawansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('no_telp')
                    ->label('No. Telepon')
                    ->searchable(),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->formatStateUsing(fn ($state): string => $state === false ? 'Nonaktif' : 'Aktif')
                    ->badge()
                    ->color(fn ($state): string => $state === false ? 'danger' : 'success')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable(),
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