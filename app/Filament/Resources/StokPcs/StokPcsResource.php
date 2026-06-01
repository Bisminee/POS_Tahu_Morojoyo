<?php

namespace App\Filament\Resources\StokPcs;

use App\Filament\Resources\StokPcs\Pages;
use App\Filament\Resources\StokPcs\Schemas\StokPcsForm;
use App\Models\StokPcs;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StokPcsResource extends Resource
{
    protected static ?string $model = StokPcs::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cube';

    protected static string | \UnitEnum | null $navigationGroup = 'Manajemen Stok';

    protected static ?string $navigationLabel = 'Stok PCS';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Stok PCS';

    protected static ?string $pluralModelLabel = 'Stok PCS';

    public static function form(Schema $schema): Schema
    {
        return StokPcsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cabang.namaCabang')
                    ->label('Cabang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pcsTahu.nama_pcs')
                    ->label('PCS Tahu')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah_stok')
                    ->label('Stok Saat Ini')
                    ->numeric()
                    ->suffix(' pcs')
                    ->badge()
                    ->color(fn ($state): string => ((int) $state) <= 10 ? 'danger' : 'success')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('id_cabang')
                    ->label('Cabang')
                    ->relationship('cabang', 'namaCabang'),

                SelectFilter::make('id_pcs_tahu')
                    ->label('PCS Tahu')
                    ->relationship('pcsTahu', 'nama_pcs'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStokPcs::route('/'),
            'create' => Pages\CreateStokPcs::route('/create'),
        ];
    }
}