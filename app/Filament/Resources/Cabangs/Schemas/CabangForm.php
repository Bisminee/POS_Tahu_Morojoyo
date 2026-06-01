<?php

namespace App\Filament\Resources\Cabangs\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class CabangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('namaCabang')
                ->label('Nama Cabang')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('is_active')
                ->label('Status')
                ->options([
                    true => 'Aktif',
                    false => 'Nonaktif',
                ])
                ->default(true)
                ->native(false)
                ->required(),

            Forms\Components\Textarea::make('alamat')
                ->label('Alamat')
                ->required()
                ->rows(3),
        ]);
    }
}