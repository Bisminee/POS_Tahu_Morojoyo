<?php

namespace App\Filament\Resources\StokPcs\Pages;

use App\Filament\Resources\StokPcs\StokPcsResource;
use App\Models\MutasiStok;
use App\Models\StokPcs;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateStokPcs extends CreateRecord
{
    protected static string $resource = StokPcsResource::class;

    protected static ?string $title = 'Kelola Stok PCS';

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $cabangId = $data['id_cabang'];
        $tipe = $data['tipe'];
        $keterangan = $data['keterangan'] ?? null;
        $items = $data['items'] ?? [];

        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => 'Minimal isi satu varian PCS tahu.',
            ]);
        }

        $lastStok = null;

        DB::transaction(function () use ($items, $cabangId, $tipe, $keterangan, &$lastStok) {
            foreach ($items as $index => $item) {
                $pcsTahuId = $item['id_pcs_tahu'] ?? null;
                $jumlah = (int) ($item['jumlah'] ?? 0);

                if (! $pcsTahuId || $jumlah < 1) {
                    throw ValidationException::withMessages([
                        "items.{$index}.jumlah" => 'Jenis PCS tahu dan jumlah wajib diisi.',
                    ]);
                }

                $stok = StokPcs::firstOrCreate(
                    [
                        'id_cabang' => $cabangId,
                        'id_pcs_tahu' => $pcsTahuId,
                    ],
                    [
                        'jumlah_stok' => 0,
                    ]
                );

                $stokSebelum = (int) $stok->jumlah_stok;

                if ($tipe === 'masuk') {
                    $stokSesudah = $stokSebelum + $jumlah;
                } else {
                    if ($jumlah > $stokSebelum) {
                        throw ValidationException::withMessages([
                            "items.{$index}.jumlah" => "Stok tidak cukup. Stok tersedia hanya {$stokSebelum} pcs.",
                        ]);
                    }

                    $stokSesudah = $stokSebelum - $jumlah;
                }

                $stok->update([
                    'jumlah_stok' => $stokSesudah,
                ]);

                MutasiStok::create([
                    'id_cabang' => $cabangId,
                    'id_pcs_tahu' => $pcsTahuId,
                    'tipe' => $tipe,
                    'jumlah' => $jumlah,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'keterangan' => $keterangan,
                ]);

                $lastStok = $stok;
            }
        });

        Notification::make()
            ->title('Stok berhasil diperbarui')
            ->body(count($items) . ' varian PCS tahu berhasil diproses.')
            ->success()
            ->send();

        if (! $lastStok) {
            throw ValidationException::withMessages([
                'items' => 'Tidak ada stok yang berhasil diproses.',
            ]);
        }

        return $lastStok;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return null;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
