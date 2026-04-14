<?php

namespace App\Filament\Resources\Karyawans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KaryawanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nama')
                ->label('Nama')
                ->required()
                ->maxLength(255),

            TextInput::make('no_telp')
                ->label('No. Telp')
                ->tel()
                ->required()
                ->maxLength(20),
        ]);
    }
}