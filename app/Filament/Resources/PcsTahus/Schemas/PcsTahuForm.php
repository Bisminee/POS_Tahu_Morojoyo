<?php

namespace App\Filament\Resources\PcsTahus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PcsTahuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nama_pcs')
                ->label('Nama PCS')
                ->required()
                ->maxLength(255),
        ]);
    }
}