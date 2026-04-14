<?php

namespace App\Filament\Resources\Cabangs\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class CabangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('Karyawan')
                ->label('Karyawan')
                ->relationship('karyawan', 'nama')
                ->searchable()
                ->preload(),

            Forms\Components\Textarea::make('Alamat')
                ->label('Alamat')
                ->required()
                ->rows(3),
        ]);
    }
}