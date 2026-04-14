<?php

namespace App\Filament\Resources\Menus;

use App\Filament\Resources\Menus\Pages\CreateMenu;
use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Models\Menu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $recordTitleAttribute = 'namaMenu';

    protected static ?string $navigationLabel = 'Menu';
    protected static ?string $modelLabel = 'Menu';
    protected static ?string $pluralModelLabel = 'Menu';

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('namaMenu')
                    ->label('Nama Menu')
                    ->searchable(),

                TextColumn::make('deskripsi')
                    ->label('Isi Menu')
                    ->wrap()
                    ->placeholder('—')
                    // ✅ Safety net: eksplisit baca dari accessor
                    // Berguna selama transisi / debugging
                    ->getStateUsing(fn($record) => $record->deskripsi),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('menuDetails.pcsTahu'); // ✅ Sudah benar, pertahankan
    }
}
