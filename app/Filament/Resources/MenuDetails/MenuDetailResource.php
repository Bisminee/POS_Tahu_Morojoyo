<?php

namespace App\Filament\Resources\MenuDetails;

use App\Filament\Resources\MenuDetails\Pages\CreateMenuDetail;
use App\Filament\Resources\MenuDetails\Pages\EditMenuDetail;
use App\Filament\Resources\MenuDetails\Pages\ListMenuDetails;
use App\Models\MenuDetail;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class MenuDetailResource extends Resource
{

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = MenuDetail::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static ?string $recordTitleAttribute = null;

    protected static ?string $navigationLabel = 'Detail Menu';
    protected static ?string $modelLabel = 'Detail Menu';
    protected static ?string $pluralModelLabel = 'Detail Menu';

    public static function getPages(): array
    {
        return [
            'index' => ListMenuDetails::route('/'),
            'create' => CreateMenuDetail::route('/create'),
            'edit' => EditMenuDetail::route('/{record}/edit'),
        ];
    }
}
