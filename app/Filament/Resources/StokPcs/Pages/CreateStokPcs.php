<?php

namespace App\Filament\Resources\StokPcs\Pages;

use App\Filament\Resources\StokPcs\Schemas\StokPcsForm;
use App\Filament\Resources\StokPcs\StokPcsResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateStokPcs extends CreateRecord
{
    protected static string $resource = StokPcsResource::class;

    public function form(Schema $schema): Schema
    {
        return StokPcsForm::configure($schema);
    }
}