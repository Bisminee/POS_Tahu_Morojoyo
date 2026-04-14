<?php

namespace App\Filament\Resources\Karyawans\Pages;

use App\Filament\Resources\Karyawans\KaryawanResource;
use App\Filament\Resources\Karyawans\Tables\KaryawansTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListKaryawans extends ListRecords
{
    protected static string $resource = KaryawanResource::class;

    public function table(Table $table): Table
    {
        return KaryawansTable::configure($table);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}