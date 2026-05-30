<?php

namespace App\Filament\Resources\StokPcs\Schemas;

use App\Models\Cabang;
use App\Models\PcsTahu;
use App\Models\StokPcs;
use Filament\Forms\Components\Placeholder;
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
                    ->description('Gunakan halaman ini untuk menambah atau mengurangi stok PCS tahu. Setiap perubahan otomatis tercatat di Mutasi Stok.')
                    ->icon('heroicon-o-cube')
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
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
                            ->required(),

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
                                    return new HtmlString(
                                        '<div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                                            Pilih cabang dan jenis PCS tahu terlebih dahulu.
                                        </div>'
                                    );
                                }

                                $stokSebelum = (int) (StokPcs::query()
                                    ->where('id_cabang', $cabangId)
                                    ->where('id_pcs_tahu', $pcsTahuId)
                                    ->value('jumlah_stok') ?? 0);

                                $stokSesudah = $tipe === 'keluar'
                                    ? $stokSebelum - $jumlah
                                    : $stokSebelum + $jumlah;

                                $warna = $stokSesudah < 0 ? 'text-red-600' : 'text-green-600';

                                return new HtmlString("
                                    <div class='rounded-xl border border-gray-200 bg-gray-50 p-4'>
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
                                ");
                            })
                            ->columnSpanFull(),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Contoh: produksi masuk, barang rusak, koreksi stok, retur, dll.')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }
}
