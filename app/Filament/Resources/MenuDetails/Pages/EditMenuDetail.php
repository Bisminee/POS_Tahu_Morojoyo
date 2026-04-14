<?php

namespace App\Filament\Resources\MenuDetails\Pages;

use App\Filament\Resources\MenuDetails\MenuDetailResource;
use App\Filament\Resources\MenuDetails\Schemas\MenuDetailForm;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditMenuDetail extends EditRecord
{
    protected static string $resource = MenuDetailResource::class;

    public function form(Schema $schema): Schema
    {
        return MenuDetailForm::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}