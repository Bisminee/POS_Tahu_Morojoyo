<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;


class AbsenShift extends Page
{
    protected string $view = 'filament.pages.absen-shift';

   protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-camera';
    protected static ?string $title = 'Absen Shift (Face Recognition)';
}
