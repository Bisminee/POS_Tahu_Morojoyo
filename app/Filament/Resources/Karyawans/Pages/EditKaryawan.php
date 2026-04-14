<?php

namespace App\Filament\Resources\Karyawans\Pages;

use App\Filament\Resources\Karyawans\KaryawanResource;
use App\Filament\Resources\Karyawans\Schemas\KaryawanForm;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditKaryawan extends EditRecord
{
    protected static string $resource = KaryawanResource::class;

    public function form(Schema $schema): Schema
    {
        return KaryawanForm::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}