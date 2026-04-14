<?php

namespace App\Filament\Resources\Karyawans;

use App\Filament\Resources\Karyawans\Pages\CreateKaryawan;
use App\Filament\Resources\Karyawans\Pages\EditKaryawan;
use App\Filament\Resources\Karyawans\Pages\ListKaryawans;
use App\Models\Karyawan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class KaryawanResource extends Resource
{
    protected static ?string $model = Karyawan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'nama';

    protected static ?string $navigationLabel = 'Karyawan';
    protected static ?string $modelLabel = 'Karyawan';
    protected static ?string $pluralModelLabel = 'Karyawan';

    /**
     * Sembunyikan resource ini dari navigasi jika user tidak punya akses.
     * Policy KaryawanPolicy::viewAny() akan menentukan siapa yang boleh.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->can('viewAny', Karyawan::class) ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListKaryawans::route('/'),
            'create' => CreateKaryawan::route('/create'),
            'edit'   => EditKaryawan::route('/{record}/edit'),
        ];
    }
}
