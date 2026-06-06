<?php

namespace App\Filament\Resources\Menus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\ToggleColumn;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('namaMenu')
                    ->label('Nama Menu')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('compositions.pcsTahu.nama_pcs')
                    ->label('Nama Tahu')
                    ->bulleted()
                    ->listWithLineBreaks(),

                TextColumn::make('jumlah_harga')
                    ->label('Harga')
                    ->state(function ($record) {
                        $jumlah = 0;

                        if (! empty($record->hargas)) {
                            $harga = $record->hargas->first();

                            if ($harga?->harga_normal !== null) {
                                $jumlah++;
                            }

                            if ($harga?->harga_gofood !== null) {
                                $jumlah++;
                            }

                            if ($harga?->harga_shopeefood !== null) {
                                $jumlah++;
                            }
                        }

                        return $jumlah > 0 ? $jumlah . ' Harga' : 'Belum Ada Harga';
                    })
                    ->badge()
                    ->color(function ($state) {
                        return $state === 'Belum Ada Harga' ? 'gray' : 'success';
                    })
                    ->action(
                        Action::make('lihatHarga')
                            ->modalHeading('Detail Harga Menu')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup')
                            ->modalContent(function ($record) {
                                $harga = $record->hargas->first();

                                if (! $harga) {
                                    return new HtmlString('<p>Belum ada data harga.</p>');
                                }

                                $normal = $harga->harga_normal !== null
                                    ? 'Rp ' . number_format($harga->harga_normal, 0, ',', '.')
                                    : '-';

                                $gofood = $harga->harga_gofood !== null
                                    ? 'Rp ' . number_format($harga->harga_gofood, 0, ',', '.')
                                    : '-';

                                $shopeefood = $harga->harga_shopeefood !== null
                                    ? 'Rp ' . number_format($harga->harga_shopeefood, 0, ',', '.')
                                    : '-';

                                $html = '
                                    <div style="display: flex; flex-direction: column; gap: 12px;">
                                        <div style="padding: 12px; border: 1px solid #e5e7eb; border-radius: 10px;">
                                            <strong>Harga Normal</strong><br>
                                            <span>' . $normal . '</span>
                                        </div>

                                        <div style="padding: 12px; border: 1px solid #e5e7eb; border-radius: 10px;">
                                            <strong>Harga GoFood</strong><br>
                                            <span>' . $gofood . '</span>
                                        </div>

                                        <div style="padding: 12px; border: 1px solid #e5e7eb; border-radius: 10px;">
                                            <strong>Harga ShopeeFood</strong><br>
                                            <span>' . $shopeefood . '</span>
                                        </div>
                                    </div>
                                ';

                                return new HtmlString($html);
                            })
                    ),
                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->onColor('success')
                    ->offColor('danger'),
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
}
