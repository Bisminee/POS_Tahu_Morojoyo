<?php

namespace App\Filament\Resources\PcsTahus\Pages;

use App\Filament\Resources\PcsTahus\PcsTahuResource;
use App\Filament\Resources\PcsTahus\Schemas\PcsTahuForm;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreatePcsTahu extends CreateRecord
{
    protected static string $resource = PcsTahuResource::class;

    public function form(Schema $schema): Schema
    {
        return PcsTahuForm::configure($schema);
    }
}