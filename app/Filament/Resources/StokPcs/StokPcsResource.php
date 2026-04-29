<?php

namespace App\Filament\Resources\StokPcs;

use App\Filament\Resources\StokPcs\Pages\CreateStokPcs;
use App\Filament\Resources\StokPcs\Pages\EditStokPcs;
use App\Filament\Resources\StokPcs\Pages\ListStokPcs;
use App\Models\StokPcs;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class StokPcsResource extends Resource
{
    protected static ?string $model = StokPcs::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $recordTitleAttribute = null;

    protected static ?string $navigationLabel = 'Stok PCS';
    protected static ?string $modelLabel = 'Stok PCS';
    protected static ?string $pluralModelLabel = 'Stok PCS';

    public static function getPages(): array
    {
        return [
            'index' => ListStokPcs::route('/'),
            'create' => CreateStokPcs::route('/create'),
            'edit' => EditStokPcs::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && !$user->isKasir();
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && !$user->isKasir();
    }

    public static function canEdit($record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && !$user->isKasir();
    }

    public static function canDelete($record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user && !$user->isKasir();
    }
}
