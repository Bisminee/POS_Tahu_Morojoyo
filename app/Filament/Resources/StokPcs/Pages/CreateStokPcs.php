<?php

namespace App\Filament\Resources\StokPcs\Pages;

use App\Filament\Resources\StokPcs\StokPcsResource;
use App\Models\MutasiStok;
use App\Models\StokPcs;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Filament\Support\Enums\Width;

class CreateStokPcs extends CreateRecord
{
    protected static string $resource = StokPcsResource::class;

    protected static ?string $title = 'Kelola Stok PCS';

    protected function handleRecordCreation(array $data): Model
    {
        $stok = StokPcs::firstOrCreate(
            [
                'id_cabang' => $data['id_cabang'],
                'id_pcs_tahu' => $data['id_pcs_tahu'],
            ],
            [
                'jumlah_stok' => 0,
            ]
        );

        $stokSebelum = (int) $stok->jumlah_stok;
        $jumlah = (int) $data['jumlah'];
        $tipe = $data['tipe'];

        if ($jumlah < 1) {
            throw ValidationException::withMessages([
                'jumlah' => 'Jumlah stok minimal 1 pcs.',
            ]);
        }

        if ($tipe === 'masuk') {
            $stokSesudah = $stokSebelum + $jumlah;
        } else {
            if ($jumlah > $stokSebelum) {
                throw ValidationException::withMessages([
                    'jumlah' => "Stok tidak cukup. Stok tersedia hanya {$stokSebelum} pcs.",
                ]);
            }

            $stokSesudah = $stokSebelum - $jumlah;
        }

        $stok->update([
            'jumlah_stok' => $stokSesudah,
        ]);

        MutasiStok::create([
            'id_cabang' => $data['id_cabang'],
            'id_pcs_tahu' => $data['id_pcs_tahu'],
            'tipe' => $tipe,
            'jumlah' => $jumlah,
            'stok_sebelum' => $stokSebelum,
            'stok_sesudah' => $stokSesudah,
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        Notification::make()
            ->title('Stok berhasil diperbarui')
            ->body("Stok berubah dari {$stokSebelum} pcs menjadi {$stokSesudah} pcs.")
            ->success()
            ->send();

        return $stok;
    }

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
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
