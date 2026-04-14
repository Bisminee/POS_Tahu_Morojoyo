<?php

namespace App\Filament\Resources\StokPcs\Pages;

use App\Filament\Resources\StokPcs\Schemas\StokPcsForm;
use App\Filament\Resources\StokPcs\StokPcsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditStokPcs extends EditRecord
{
    protected static string $resource = StokPcsResource::class;

    public function form(Schema $schema): Schema
    {
        return StokPcsForm::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}