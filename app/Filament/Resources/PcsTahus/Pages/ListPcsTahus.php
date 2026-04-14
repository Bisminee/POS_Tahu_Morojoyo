<?php

namespace App\Filament\Resources\PcsTahus\Pages;

use App\Filament\Resources\PcsTahus\PcsTahuResource;
use App\Filament\Resources\PcsTahus\Tables\PcsTahusTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListPcsTahus extends ListRecords
{
    protected static string $resource = PcsTahuResource::class;

    public function table(Table $table): Table
    {
        return PcsTahusTable::configure($table);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}