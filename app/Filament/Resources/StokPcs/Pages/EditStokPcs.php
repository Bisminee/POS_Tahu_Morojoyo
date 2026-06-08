<?php

namespace App\Filament\Resources\StokPcs\Pages;

use App\Filament\Resources\StokPcs\StokPcsResource;
use App\Models\MutasiStok;
use App\Models\StokPcs;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditStokPcs extends EditRecord
{
    protected static string $resource = \App\Filament\Resources\StokPcs\StokPcsResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['tipe']       = 'masuk';
        $data['jumlah']     = 0;
        $data['keterangan'] = null;
        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        /** @var StokPcs $stok */
        $stok = StokPcs::where('id_cabang', $data['id_cabang'])
            ->where('id_pcs_tahu', $data['id_pcs_tahu'])
            ->firstOrCreate(
                [
                    'id_cabang'   => $data['id_cabang'],
                    'id_pcs_tahu' => $data['id_pcs_tahu'],
                ],
                ['jumlah_stok' => 0]
            );

        $stokSebelum = $stok->jumlah_stok;
        $jumlah      = (int) $data['jumlah'];
        $tipe        = $data['tipe'];

        if ($jumlah === 0) {
            Notification::make()
                ->title('Jumlah tidak boleh 0')
                ->warning()
                ->send();

            return $record;
        }

        $stokSesudah = $tipe === 'masuk'
            ? $stokSebelum + $jumlah
            : max(0, $stokSebelum - $jumlah);

        $stok->update(['jumlah_stok' => $stokSesudah]);

        MutasiStok::create([
            'id_cabang'    => $data['id_cabang'],
            'id_pcs_tahu'  => $data['id_pcs_tahu'],
            'tipe'         => $tipe,
            'jumlah'       => $jumlah,
            'stok_sebelum' => $stokSebelum,
            'stok_sesudah' => $stokSesudah,
            'keterangan'   => $data['keterangan'] ?? null,
        ]);

        $label = $tipe === 'masuk' ? 'Masuk' : 'Keluar';

        Notification::make()
            ->title('Stok berhasil diperbarui')
            ->body("{$label}: {$stokSebelum} → {$stokSesudah} pcs")
            ->success()
            ->send();

        return $stok;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}