<?php

namespace App\Filament\Resources\StokPcs;

use App\Filament\Resources\StokPcs\Pages;
use App\Models\Cabang;
use App\Models\PcsTahu;
use App\Models\StokPcs;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class StokPcsResource extends Resource
{
    protected static ?string $model = StokPcs::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cube';

    protected static string | \UnitEnum | null $navigationGroup = 'Manajemen Stok';

    protected static ?string $navigationLabel = 'Stok PCS';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Stok PCS';

    protected static ?string $pluralModelLabel = 'Stok PCS';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kelola Stok PCS')
                    ->description('Gunakan halaman ini untuk menambah atau mengurangi stok PCS tahu. Setiap perubahan otomatis tercatat di Mutasi Stok.')
                    ->icon('heroicon-o-cube')
                    ->columns(2)
                    ->schema([
                        Select::make('id_cabang')
                            ->label('Cabang')
                            ->options(fn (): array => Cabang::query()
                                ->orderBy('namaCabang')
                                ->pluck('namaCabang', 'idCabang')
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),

                        Select::make('id_pcs_tahu')
                            ->label('Jenis PCS Tahu')
                            ->options(fn (): array => PcsTahu::query()
                                ->orderBy('nama_pcs')
                                ->pluck('nama_pcs', 'id_pcs')
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),

                        Select::make('tipe')
                            ->label('Tipe Perubahan')
                            ->options([
                                'masuk' => 'Tambah Stok',
                                'keluar' => 'Kurangi Stok',
                            ])
                            ->default('masuk')
                            ->native(false)
                            ->live()
                            ->required(),

                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->suffix('pcs')
                            ->live(debounce: 500)
                            ->required(),

                        Placeholder::make('preview_stok')
                            ->label('Preview Stok')
                            ->content(function (Get $get): HtmlString {
                                $cabangId = $get('id_cabang');
                                $pcsTahuId = $get('id_pcs_tahu');
                                $tipe = $get('tipe') ?: 'masuk';
                                $jumlah = (int) ($get('jumlah') ?: 0);

                                if (! $cabangId || ! $pcsTahuId) {
                                    return new HtmlString('<span class="text-sm text-gray-500">Pilih cabang dan jenis PCS tahu terlebih dahulu.</span>');
                                }

                                $stokSebelum = (int) (StokPcs::query()
                                    ->where('id_cabang', $cabangId)
                                    ->where('id_pcs_tahu', $pcsTahuId)
                                    ->value('jumlah_stok') ?? 0);

                                $stokSesudah = $tipe === 'keluar'
                                    ? $stokSebelum - $jumlah
                                    : $stokSebelum + $jumlah;

                                $warna = $stokSesudah < 0 ? 'text-red-600' : 'text-gray-900';

                                return new HtmlString("<div class='rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm'>
                                    <div>Stok sekarang: <strong>{$stokSebelum} pcs</strong></div>
                                    <div>Stok setelah perubahan: <strong class='{$warna}'>{$stokSesudah} pcs</strong></div>
                                </div>");
                            })
                            ->columnSpanFull(),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Contoh: produksi masuk, barang rusak, koreksi stok, retur, dll.')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cabang.namaCabang')
                    ->label('Cabang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pcsTahu.nama_pcs')
                    ->label('PCS Tahu')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah_stok')
                    ->label('Stok Saat Ini')
                    ->numeric()
                    ->suffix(' pcs')
                    ->badge()
                    ->color(fn ($state): string => ((int) $state) <= 10 ? 'danger' : 'success')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('id_cabang')
                    ->label('Cabang')
                    ->relationship('cabang', 'namaCabang'),

                SelectFilter::make('id_pcs_tahu')
                    ->label('PCS Tahu')
                    ->relationship('pcsTahu', 'nama_pcs'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStokPcs::route('/'),
            'create' => Pages\CreateStokPcs::route('/create'),
        ];
    }
}
