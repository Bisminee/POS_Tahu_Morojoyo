<?php

namespace App\Filament\Resources\PcsTahus\Pages;

use App\Filament\Resources\PcsTahus\PcsTahuResource;
use App\Filament\Resources\PcsTahus\Schemas\PcsTahuForm;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditPcsTahu extends EditRecord
{
    protected static string $resource = PcsTahuResource::class;

    public function form(Schema $schema): Schema
    {
        return PcsTahuForm::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}