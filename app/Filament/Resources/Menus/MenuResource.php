<?php

namespace App\Filament\Resources\Menus;

use App\Filament\Resources\Menus\Pages\CreateMenu;
use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Models\Menu;
use App\Models\PcsTahu;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string | \UnitEnum | null $navigationGroup = 'Master Data';

    protected static ?string $recordTitleAttribute = 'namaMenu';

    protected static ?string $navigationLabel = 'Menu';

    protected static ?string $modelLabel = 'Menu';

    protected static ?string $pluralModelLabel = 'Menu';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('namaMenu')
                    ->label('Nama Menu')
                    ->required()
                    ->maxLength(255),

                FileUpload::make('foto')
                    ->label('Foto Produk')
                    ->image()
                    ->directory('menus')
                    ->disk('public'),

                TextInput::make('tagline_product')
                    ->label('Tagline Product')
                    ->maxLength(255),

                Textarea::make('deskripsi_produk')
                    ->label('Deskripsi Produk')
                    ->rows(4)
                    ->columnSpanFull(),

                Textarea::make('deskripsi')
                    ->label('Isi Menu')
                    ->rows(3)
                    ->placeholder('Contoh: Tahu bakso isi keju, original, dan pedas.')
                    ->columnSpanFull(),

                Repeater::make('menuDetails')
                    ->label('Komposisi Menu')
                    ->relationship('menuDetails')
                    ->schema([
                        Select::make('id_pcs')
                            ->label('Jenis Tahu')
                            ->options(fn () => PcsTahu::query()
                                ->orderBy('nama_pcs')
                                ->pluck('nama_pcs', 'id_pcs'))
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('jumlah_pcs')
                            ->label('Jumlah Pakai')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                    ])
                    ->columns(2)
                    ->defaultItems(1)
                    ->addActionLabel('Tambah Komposisi')
                    ->reorderable(false)
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->square(),

                TextColumn::make('namaMenu')
                    ->label('Nama Menu')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('menuDetails.pcsTahu.nama_pcs')
                    ->label('Komposisi')
                    ->bulleted()
                    ->listWithLineBreaks()
                    ->placeholder('Belum ada komposisi'),

                TextColumn::make('jumlah_komposisi')
                    ->label('Jumlah Komposisi')
                    ->state(fn (Menu $record): string => $record->menuDetails->count() . ' Item')
                    ->badge()
                    ->color(fn (string $state): string => $state === '0 Item' ? 'gray' : 'success'),

                TextColumn::make('jumlah_harga')
                    ->label('Harga')
                    ->state(function (Menu $record): string {
                        $harga = $record->hargas->first();

                        if (! $harga) {
                            return 'Belum Ada Harga';
                        }

                        $jumlah = 0;

                        if ($harga->harga_normal !== null) {
                            $jumlah++;
                        }

                        if ($harga->harga_gofood !== null) {
                            $jumlah++;
                        }

                        if ($harga->harga_shopeefood !== null) {
                            $jumlah++;
                        }

                        return $jumlah > 0 ? $jumlah . ' Harga' : 'Belum Ada Harga';
                    })
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Belum Ada Harga' ? 'gray' : 'success'),

                TextColumn::make('tagline_product')
                    ->label('Tagline')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('deskripsi_produk')
                    ->label('Deskripsi Produk')
                    ->limit(60)
                    ->wrap(),

                TextColumn::make('deskripsi')
                    ->label('Isi Menu')
                    ->wrap()
                    ->limit(60)
                    ->placeholder('—'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'menuDetails.pcsTahu',
                'hargas',
            ]);
    }
}