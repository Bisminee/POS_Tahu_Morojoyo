<?php

namespace App\Filament\Resources\StokPcs\Pages;

use App\Filament\Resources\StokPcs\StokPcsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStokPcs extends ListRecords
{
    protected static string $resource = StokPcsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah / Kurangi Stok')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
