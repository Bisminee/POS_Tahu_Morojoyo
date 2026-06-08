<?php

namespace App\Filament\Resources\Identitas\Pages;

use App\Filament\Resources\Identitas\IdentitasResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIdentitas extends EditRecord
{
    protected static string $resource = IdentitasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
