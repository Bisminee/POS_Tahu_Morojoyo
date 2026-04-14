<?php

namespace App\Filament\Resources\Cabangs\Pages;

use App\Filament\Resources\Cabangs\CabangResource;
use App\Filament\Resources\Cabangs\Schemas\CabangForm;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditCabang extends EditRecord
{
    protected static string $resource = CabangResource::class;

    public function form(Schema $schema): Schema
    {
        return CabangForm::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}