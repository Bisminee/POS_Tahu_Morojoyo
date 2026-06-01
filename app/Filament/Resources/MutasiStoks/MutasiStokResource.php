<?php

namespace App\Filament\Resources\MutasiStoks;

use App\Filament\Resources\MutasiStoks\Pages\CreateMutasiStok;
use App\Filament\Resources\MutasiStoks\Pages\EditMutasiStok;
use App\Filament\Resources\MutasiStoks\Pages\ListMutasiStoks;
use App\Filament\Resources\MutasiStoks\Schemas\MutasiStokForm;
use App\Filament\Resources\MutasiStoks\Tables\MutasiStoksTable;
use App\Models\MutasiStok;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MutasiStokResource extends Resource
{
    protected static ?string $model = MutasiStok::class;

    protected static ?string $navigationLabel = 'Mutasi Stok';

    protected static ?int $navigationSort = 2;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-path';

    protected static string | \UnitEnum | null $navigationGroup = 'Manajemen Stok';

    public static function form(Schema $schema): Schema
    {
        return MutasiStokForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MutasiStoksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMutasiStoks::route('/'),
        ];
    }
}
