<?php

namespace App\Filament\Resources\Karyawans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KaryawanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nama')
                ->label('Nama Karyawan')
                ->required()
                ->maxLength(255),

            TextInput::make('no_telp')
                ->label('No. Telepon')
                ->tel()
                ->required()
                ->maxLength(20),

            Select::make('is_active')
                ->label('Status')
                ->options([
                    true => 'Aktif',
                    false => 'Nonaktif',
                ])
                ->default(true)
                ->native(false)
                ->required(),
        ]);
    }
}