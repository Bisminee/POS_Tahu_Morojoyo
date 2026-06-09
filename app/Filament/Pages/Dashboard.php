<?php

namespace App\Filament\Pages;

use App\Models\Cabang;
use App\Models\Menu;
use App\Models\Karyawan;
use App\Models\StokPcs;
use App\Models\Transaction;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Dashboard extends BaseDashboard
{
    // protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getHeaderWidgetsData(): array
    {
        return [
            'totalCabang'    => Cabang::count(),
            'totalMenu'      => Menu::count(),
            'totalKaryawan'  => Karyawan::count(),
            'totalStok'      => StokPcs::sum('jumlah_stok'),       // ✅ nama kolom yang benar
            'totalPendapatan'=> Transaction::where('status', 'completed')->sum('total'),
        ];
    }
}