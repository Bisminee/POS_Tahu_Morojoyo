<?php

namespace App\Filament\Resources\Hargas\Pages;

use App\Filament\Resources\Hargas\HargaResource;
use App\Filament\Resources\Hargas\Tables\HargasTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListHargas extends ListRecords
{
    protected static string $resource = HargaResource::class;

    public function table(Table $table): Table
    {
        return HargasTable::configure($table);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}