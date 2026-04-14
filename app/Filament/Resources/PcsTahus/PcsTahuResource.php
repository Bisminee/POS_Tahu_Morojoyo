<?php

namespace App\Filament\Resources\PcsTahus;

use App\Filament\Resources\PcsTahus\Pages\CreatePcsTahu;
use App\Filament\Resources\PcsTahus\Pages\EditPcsTahu;
use App\Filament\Resources\PcsTahus\Pages\ListPcsTahus;
use App\Models\PcsTahu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class PcsTahuResource extends Resource
{
    protected static ?string $model = PcsTahu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $recordTitleAttribute = 'namaPcs';

    protected static ?string $navigationLabel = 'PCS Tahu';
    protected static ?string $modelLabel = 'PCS Tahu';
    protected static ?string $pluralModelLabel = 'PCS Tahu';

    public static function getPages(): array
    {
        return [
            'index' => ListPcsTahus::route('/'),
            'create' => CreatePcsTahu::route('/create'),
            'edit' => EditPcsTahu::route('/{record}/edit'),
        ];
    }
}