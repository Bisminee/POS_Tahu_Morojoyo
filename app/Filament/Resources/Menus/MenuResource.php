<?php

namespace App\Filament\Resources\Menus;

use App\Filament\Resources\Menus\Pages\CreateMenu;
use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Models\Menu;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $recordTitleAttribute = 'nama_menu';

    protected static ?string $navigationLabel = 'Menu';
    protected static ?string $modelLabel = 'Menu';
    protected static ?string $pluralModelLabel = 'Menu';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_menu')
                    ->label('Nama Menu')
                    ->required()
                    ->maxLength(255),

                Select::make('rasa')
                    ->label('Rasa')
                    ->options([
                        'keju' => 'Keju',
                        'ori' => 'Original',
                        'pedas' => 'Pedas',
                        'spicy_cheese' => 'Spicy Cheese',
                        'mix_keju_ori_pedas' => 'Mix Keju Ori Pedas',
                    ])
                    ->required(),

                Select::make('ukuran')
                    ->label('Ukuran')
                    ->options([
                        'small' => 'Small',
                        'medium' => 'Medium',
                        'large' => 'Large',
                    ])
                    ->required(),

                TextInput::make('harga')
                    ->label('Harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->minValue(0),

                Toggle::make('is_active')
                    ->label('Menu Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_menu')
                    ->label('Nama Menu')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rasa')
                    ->label('Rasa')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'keju' => 'Keju',
                            'ori' => 'Original',
                            'pedas' => 'Pedas',
                            'spicy_cheese' => 'Spicy Cheese',
                            'mix_keju_ori_pedas' => 'Mix Keju Ori Pedas',
                            default => $state,
                        };
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ukuran')
                    ->label('Ukuran')
                    ->badge()
                    ->sortable(),

                TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('rasa')
                    ->label('Filter Rasa')
                    ->options([
                        'keju' => 'Keju',
                        'ori' => 'Original',
                        'pedas' => 'Pedas',
                        'spicy_cheese' => 'Spicy Cheese',
                        'mix_keju_ori_pedas' => 'Mix Keju Ori Pedas',
                    ]),

                SelectFilter::make('ukuran')
                    ->label('Filter Ukuran')
                    ->options([
                        'small' => 'Small',
                        'medium' => 'Medium',
                        'large' => 'Large',
                    ]),

                SelectFilter::make('is_active')
                    ->label('Status Menu')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Nonaktif',
                    ]),
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
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}