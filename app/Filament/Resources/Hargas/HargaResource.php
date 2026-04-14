<?php

namespace App\Filament\Resources\Hargas;

use App\Filament\Resources\Hargas\Pages\CreateHarga;
use App\Filament\Resources\Hargas\Pages\EditHarga;
use App\Filament\Resources\Hargas\Pages\ListHargas;
use App\Models\Harga;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class HargaResource extends Resource
{
    protected static ?string $model = Harga::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $recordTitleAttribute = null;

    protected static ?string $navigationLabel = 'Harga';
    protected static ?string $modelLabel = 'Harga';
    protected static ?string $pluralModelLabel = 'Harga';

    public static function getPages(): array
    {
        return [
            'index' => ListHargas::route('/'),
            'create' => CreateHarga::route('/create'),
            'edit' => EditHarga::route('/{record}/edit'),
        ];
    }
}