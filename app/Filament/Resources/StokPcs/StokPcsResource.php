<?php

namespace App\Filament\Resources\StokPcs;

use App\Filament\Resources\StokPcs\Pages\CreateStokPcs;
use App\Filament\Resources\StokPcs\Pages\EditStokPcs;
use App\Filament\Resources\StokPcs\Pages\ListStokPcs;
use App\Models\StokPcs;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StokPcsResource extends Resource
{
    protected static ?string $model = StokPcs::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $navigationLabel = 'Stok PCS';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_cabang') 
                    ->label('Cabang')
                    ->relationship('cabang', 'namaCabang')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('id_pcs_tahu') 
                    ->label('PCS Tahu')
                    ->relationship('pcsTahu', 'nama_pcs')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('jumlah_stok')
                    ->label('Jumlah Stok')
                    ->numeric()
                    ->required()
                    ->minValue(0),
            ]);
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
                    ->label('Jumlah Stok')
                    ->sortable(),

                TextColumn::make('status_stok')
                    ->label('Status Stok')
                    ->getStateUsing(function ($record) {
                        if ($record->jumlah_stok <= 0) {
                            return 'Habis';
                        }

                        if ($record->jumlah_stok <= 10) {
                            return 'Menipis';
                        }

                        return 'Aman';
                    })
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'Habis' => 'danger',
                            'Menipis' => 'warning',
                            'Aman' => 'success',
                            default => 'gray',
                        };
                    }),
            ])
            ->filters([
                SelectFilter::make('id_cabang') 
                    ->label('Filter Cabang')
                    ->relationship('cabang', 'namaCabang'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStokPcs::route('/'),
            'create' => CreateStokPcs::route('/create'),
            'edit' => EditStokPcs::route('/{record}/edit'),
        ];
    }
}