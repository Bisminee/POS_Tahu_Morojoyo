<?php

namespace App\Filament\Resources\Cabangs;

use App\Filament\Resources\Cabangs\Pages\CreateCabang;
use App\Filament\Resources\Cabangs\Pages\EditCabang;
use App\Filament\Resources\Cabangs\Pages\ListCabangs;
use App\Models\Cabang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class CabangResource extends Resource
{
    protected static ?string $model = Cabang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $recordTitleAttribute = 'Alamat';

    protected static ?string $navigationLabel = 'Cabang';
    protected static ?string $modelLabel = 'Cabang';
    protected static ?string $pluralModelLabel = 'Cabang';

    public static function getPages(): array
    {
        return [
            'index' => ListCabangs::route('/'),
            'create' => CreateCabang::route('/create'),
            'edit' => EditCabang::route('/{record}/edit'),
        ];
    }
}