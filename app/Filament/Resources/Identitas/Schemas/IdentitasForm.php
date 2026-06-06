<?php

namespace App\Filament\Resources\Identitas\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class IdentitasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('nama_brand')
                    ->label('Nama Brand')
                    ->required()
                    ->maxLength(255),

                Textarea::make('deskripsi_brand')
                    ->label('Deskripsi Brand')
                    ->rows(4)
                    ->required(),

                TextInput::make('nomor_whatsapp')
                    ->label('Nomor WhatsApp')
                    ->required()
                    ->maxLength(20),

                TextInput::make('nama_ig')
                    ->label('Nama Instagram')
                    ->required()
                    ->maxLength(255),

                TextInput::make('link_wa')
                    ->label('Link WhatsApp')
                    ->url()
                    ->required(),

                TextInput::make('link_ig')
                    ->label('Link Instagram')
                    ->url()
                    ->required(),

                TimePicker::make('jam_buka')
                    ->label('Jam Buka')
                    ->required(),

                TimePicker::make('jam_tutup')
                    ->label('Jam Tutup')
                    ->required(),

                FileUpload::make('logo')
                    ->label('Logo Brand')
                    ->image()
                    ->directory('logo')
                    ->imagePreviewHeight('150')
                    ->columnSpanFull(),

                FileUpload::make('promo')
                    ->label('Banner Promo')
                    ->image()
                    ->directory('promo')
                    ->imagePreviewHeight('200')
                    ->columnSpanFull(),

            ]);
    }
}