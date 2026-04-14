<?php

namespace App\Filament\Resources\Cabangs\Pages;

use App\Filament\Resources\Cabangs\CabangResource;
use App\Filament\Resources\Cabangs\Schemas\CabangForm;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateCabang extends CreateRecord
{
    protected static string $resource = CabangResource::class;

    public function form(Schema $schema): Schema
    {
        return CabangForm::configure($schema);
    }
}