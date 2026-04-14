<?php

namespace App\Filament\Resources\Menus\Pages;

use App\Filament\Resources\Menus\MenuResource;
use App\Filament\Resources\Menus\Schemas\MenuForm;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;

    public function form(Schema $schema): Schema
    {
        return MenuForm::configure($schema);
    }
}