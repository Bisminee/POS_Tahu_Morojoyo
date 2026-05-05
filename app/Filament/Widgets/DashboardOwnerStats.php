<?php

namespace App\Filament\Widgets;

use App\Models\Cabang;
use App\Models\Harga;
use App\Models\Karyawan;
use App\Models\Menu;
use App\Models\StokPcs;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardOwnerStats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCabang = Cabang::count();
        $totalMenu = Menu::count();
        $totalKaryawan = Karyawan::count();
        $totalHarga = Harga::count();

        $totalStokPcs = StokPcs::sum('jumlah_stok');

        $stokMenipis = StokPcs::where('jumlah_stok', '<=', 10)
            ->where('jumlah_stok', '>', 0)
            ->count();

        $menuBelumPunyaHarga = Menu::doesntHave('hargas')->count();

        return [
            Stat::make('Total Cabang', $totalCabang)
                ->description('Jumlah cabang yang terdaftar'),

            Stat::make('Total Menu', $totalMenu)
                ->description('Jumlah menu yang tersedia'),

            Stat::make('Total Karyawan', $totalKaryawan)
                ->description('Jumlah karyawan aktif'),

            Stat::make('Total Stok PCS', $totalStokPcs)
                ->description('Akumulasi seluruh stok PCS'),

            Stat::make('Menu Belum Punya Harga', $menuBelumPunyaHarga)
                ->description('Perlu dilengkapi data harga'),

            Stat::make('Stok Menipis', $stokMenipis)
                ->description('Stok 10 ke bawah'),
        ];
    }
}