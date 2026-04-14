<?php

namespace App\Filament\Resources\Cabangs\Pages;

use App\Filament\Resources\Cabangs\CabangResource;
use App\Filament\Resources\Cabangs\Tables\CabangsTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCabangs extends ListRecords
{
    protected static string $resource = CabangResource::class;

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return CabangsTable::configure($table);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}