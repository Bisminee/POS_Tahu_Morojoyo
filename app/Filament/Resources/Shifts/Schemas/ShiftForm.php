<?php

namespace App\Filament\Resources\Shifts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class ShiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // KARYAWAN
            Select::make('karyawan_id')
                ->label('Karyawan')
                ->relationship('karyawan', 'nama')
                ->searchable()
                ->preload()
                ->required(),

            // CABANG PENEMPATAN SHIFT
            Select::make('cabang_id')
                ->label('Cabang shift')
                ->relationship('cabang', 'namaCabang')
                ->searchable()
                ->preload()
                ->required(),

            // SESI
            Select::make('sesi')
                ->label('Sesi')
                ->options([
                    'siang' => 'Siang',
                    'sore'  => 'Sore',
                ])
                ->required(),

            // TANGGAL
            DatePicker::make('tanggal')
                ->label('Tanggal')
                ->required(),

            // JAM MULAI
            TimePicker::make('jam_mulai')
                ->label('Jam Mulai')
                ->seconds(false)
                ->required(),

            // JAM SELESAI
            TimePicker::make('jam_selesai')
                ->label('Jam Selesai')
                ->seconds(false)
                ->required()
                ->after('jam_mulai'),

            // TOLERANSI
            TextInput::make('toleransi_menit')
                ->label('Toleransi Telat (menit)')
                ->numeric()
                ->default(15)
                ->minValue(0)
                ->maxValue(60),

        ]);
    }
}