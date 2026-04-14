<?php

namespace App\Filament\Resources\Karyawans\Pages;

use App\Filament\Resources\Karyawans\KaryawanResource;
use App\Filament\Resources\Karyawans\Schemas\KaryawanForm;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateKaryawan extends CreateRecord
{
    protected static string $resource = KaryawanResource::class;

    public function form(Schema $schema): Schema
    {
        return KaryawanForm::configure($schema);
    }
}