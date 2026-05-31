<?php

namespace App\Filament\Resources\StokPcs\Schemas;

use App\Models\Cabang;
use App\Models\PcsTahu;
use App\Models\StokPcs;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class StokPcsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kelola Stok PCS')
                    ->description('Gunakan halaman ini untuk menambah atau mengurangi beberapa varian PCS tahu sekaligus. Setiap perubahan otomatis tercatat di Mutasi Stok.')
                    ->icon('heroicon-o-cube')
                    ->schema([
                        Select::make('id_cabang')
                            ->label('Cabang')
                            ->options(fn (): array => Cabang::query()
                                ->orderBy('namaCabang')
                                ->pluck('namaCabang', 'idCabang')
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->required()
                            ->columnSpanFull(),

                        Select::make('tipe')
                            ->label('Tipe Perubahan')
                            ->options([
                                'masuk' => 'Tambah Stok',
                                'keluar' => 'Kurangi Stok',
                            ])
                            ->default('masuk')
                            ->native(false)
                            ->live()
                            ->required()
                            ->columnSpanFull(),

                        Repeater::make('items')
                            ->label('Detail PCS Tahu')
                            ->schema([
                                Select::make('id_pcs_tahu')
                                    ->label('Jenis PCS Tahu')
                                    ->options(fn (): array => PcsTahu::query()
                                        ->orderBy('nama_pcs')
                                        ->pluck('nama_pcs', 'id_pcs')
                                        ->toArray())
                                    ->searchable()
                                    ->preload()
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
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->addActionLabel('Tambah Varian PCS')
                            ->reorderable(false)
                            ->columnSpanFull()
                            ->required(),

                        Placeholder::make('preview_stok')
                            ->label('Preview Stok')
                            ->content(function (Get $get): HtmlString {
                                $cabangId = $get('id_cabang');
                                $tipe = $get('tipe') ?: 'masuk';
                                $items = $get('items') ?? [];

                                if (! $cabangId || empty($items)) {
                                    return new HtmlString(
                                        '<div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                                            Pilih cabang dan isi detail PCS tahu terlebih dahulu.
                                        </div>'
                                    );
                                }

                                $html = '<div class="space-y-3">';

                                foreach ($items as $item) {
                                    $pcsTahuId = $item['id_pcs_tahu'] ?? null;
                                    $jumlah = (int) ($item['jumlah'] ?? 0);

                                    if (! $pcsTahuId || $jumlah < 1) {
                                        continue;
                                    }

                                    $pcsTahu = PcsTahu::query()->find($pcsTahuId);
                                    $namaPcs = $pcsTahu?->nama_pcs ?? 'PCS Tahu';

                                    $stokSebelum = (int) (StokPcs::query()
                                        ->where('id_cabang', $cabangId)
                                        ->where('id_pcs_tahu', $pcsTahuId)
                                        ->value('jumlah_stok') ?? 0);

                                    $stokSesudah = $tipe === 'keluar'
                                        ? $stokSebelum - $jumlah
                                        : $stokSebelum + $jumlah;

                                    $warna = $stokSesudah < 0 ? 'text-red-600' : 'text-green-600';

                                    $html .= "
                                        <div class='rounded-xl border border-gray-200 bg-gray-50 p-4'>
                                            <div class='mb-2 font-semibold text-gray-900'>{$namaPcs}</div>
                                            <div class='grid gap-3 md:grid-cols-3'>
                                                <div>
                                                    <div class='text-xs text-gray-500'>Stok Sekarang</div>
                                                    <div class='text-lg font-semibold text-gray-900'>{$stokSebelum} pcs</div>
                                                </div>
                                                <div>
                                                    <div class='text-xs text-gray-500'>Jumlah Perubahan</div>
                                                    <div class='text-lg font-semibold text-gray-900'>{$jumlah} pcs</div>
                                                </div>
                                                <div>
                                                    <div class='text-xs text-gray-500'>Stok Setelah Perubahan</div>
                                                    <div class='text-lg font-semibold {$warna}'>{$stokSesudah} pcs</div>
                                                </div>
                                            </div>
                                        </div>
                                    ";
                                }

                                $html .= '</div>';

                                return new HtmlString($html);
                            })
                            ->columnSpanFull(),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Contoh: produksi masuk, barang rusak, koreksi stok, retur, dll.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }
}