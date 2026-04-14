<?php

namespace App\Filament\Resources\MenuDetails\Pages;

use App\Filament\Resources\MenuDetails\MenuDetailResource;
use App\Filament\Resources\MenuDetails\Schemas\MenuDetailForm;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateMenuDetail extends CreateRecord
{
    protected static string $resource = MenuDetailResource::class;

    public function form(Schema $schema): Schema
    {
        return MenuDetailForm::configure($schema);
    }
}