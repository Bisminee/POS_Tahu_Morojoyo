<?php

namespace App\Filament\Resources\StokPcs\Pages;

use App\Filament\Resources\StokPcs\StokPcsResource;
use App\Filament\Resources\StokPcs\Tables\StokPcsTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListStokPcs extends ListRecords
{
    protected static string $resource = StokPcsResource::class;

    public function table(Table $table): Table
    {
        return StokPcsTable::configure($table);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}