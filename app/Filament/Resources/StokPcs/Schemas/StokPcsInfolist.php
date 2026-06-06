<?php

namespace App\Filament\Resources\StokPcs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StokPcsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id_cabang')
                    ->numeric(),
                TextEntry::make('id_pcs_tahu')
                    ->numeric(),
                TextEntry::make('jumlah_stok')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
