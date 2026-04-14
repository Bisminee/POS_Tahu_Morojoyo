<?php

namespace App\Filament\Resources\MenuDetails\Pages;

use App\Filament\Resources\MenuDetails\MenuDetailResource;
use App\Filament\Resources\MenuDetails\Tables\MenuDetailsTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListMenuDetails extends ListRecords
{
    protected static string $resource = MenuDetailResource::class;

    public function table(Table $table): Table
    {
        return MenuDetailsTable::configure($table);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}