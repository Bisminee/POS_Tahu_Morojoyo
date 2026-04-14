<?php

namespace App\Filament\Resources\Hargas\Pages;

use App\Filament\Resources\Hargas\HargaResource;
use App\Filament\Resources\Hargas\Schemas\HargaForm;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateHarga extends CreateRecord
{
    protected static string $resource = HargaResource::class;

    public function form(Schema $schema): Schema
    {
        return HargaForm::configure($schema);
    }
}