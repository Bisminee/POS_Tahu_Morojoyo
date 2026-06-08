<?php

namespace App\Filament\Resources\PcsTahus\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PcsTahusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_pcs')
                    ->label('Nama PCS')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->formatStateUsing(fn ($state): string => $state ? 'Aktif' : 'Nonaktif')
                    ->badge()
                    ->color(fn ($state): string => $state ? 'success' : 'danger')
                    ->sortable(),

                TextColumn::make('menu_compositions_count')
                    ->label('Dipakai Menu')
                    ->counts('menuCompositions')
                    ->formatStateUsing(fn ($state): string => $state . ' Menu')
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
                    ->visible(fn ($record): bool => (bool) $record->is_active)
                    ->action(fn ($record) => $record->update(['is_active' => false])),

                Action::make('aktifkan')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => ! $record->is_active)
                    ->action(fn ($record) => $record->update(['is_active' => true])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}