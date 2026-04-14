<?php

namespace App\Filament\Resources\Hargas\Pages;

use App\Filament\Resources\Hargas\HargaResource;
use App\Filament\Resources\Hargas\Schemas\HargaForm;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditHarga extends EditRecord
{
    protected static string $resource = HargaResource::class;

    public function form(Schema $schema): Schema
    {
        return HargaForm::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}