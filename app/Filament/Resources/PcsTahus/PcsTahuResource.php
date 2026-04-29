<?php

namespace App\Filament\Resources\PcsTahus;

use App\Filament\Resources\PcsTahus\Pages\CreatePcsTahu;
use App\Filament\Resources\PcsTahus\Pages\EditPcsTahu;
use App\Filament\Resources\PcsTahus\Pages\ListPcsTahus;
use App\Models\PcsTahu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class PcsTahuResource extends Resource
{
    protected static ?string $model = PcsTahu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $recordTitleAttribute = 'nama_pcs';

    protected static ?string $navigationLabel = 'PCS Tahu';
    protected static ?string $modelLabel = 'PCS Tahu';
    protected static ?string $pluralModelLabel = 'PCS Tahu';

    public static function getPages(): array
    {
        return [
            'index' => ListPcsTahus::route('/'),
            'create' => CreatePcsTahu::route('/create'),
            'edit' => EditPcsTahu::route('/{record}/edit'),
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
