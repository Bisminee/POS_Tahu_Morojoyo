<?php

namespace App\Filament\Widgets;

use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Menu;
use App\Models\PcsTahu;
use App\Models\StokPcs;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardOwnerStats extends BaseWidget
{
    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $totalCabang = Cabang::count();
        $totalMenu = Menu::count();
        $totalKaryawan = Karyawan::count();
        $totalPcsTahu = PcsTahu::count();
        $totalStokPcs = StokPcs::sum('jumlah_stok');

        $stokMenipis = StokPcs::where('jumlah_stok', '<=', 10)
            ->where('jumlah_stok', '>', 0)
            ->count();

        $menuBelumPunyaHarga = Menu::doesntHave('hargas')->count();

        return [
            Stat::make('Total Cabang', $totalCabang)
                ->description('Cabang terdaftar')
                ->icon('heroicon-o-building-storefront')
                ->color('gray'),

            Stat::make('Total Menu', $totalMenu)
                ->description('Menu tersedia')
                ->icon('heroicon-o-book-open')
                ->color('gray'),

            Stat::make('Total Karyawan', $totalKaryawan)
                ->description('Karyawan terdaftar')
                ->icon('heroicon-o-users')
                ->color('gray'),

            Stat::make('Total PCS Tahu', $totalPcsTahu)
                ->description('Jenis PCS tahu')
                ->icon('heroicon-o-cube')
                ->color('gray'),

            Stat::make('Total Stok PCS', $totalStokPcs)
                ->description('Akumulasi stok PCS')
                ->icon('heroicon-o-archive-box')
                ->color('gray'),

            Stat::make('Stok Menipis', $stokMenipis)
                ->description('Stok 10 pcs ke bawah')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($stokMenipis > 0 ? 'danger' : 'success'),

            Stat::make('Menu Belum Punya Harga', $menuBelumPunyaHarga)
                ->description('Perlu dilengkapi')
                ->icon('heroicon-o-banknotes')
                ->color($menuBelumPunyaHarga > 0 ? 'warning' : 'success'),
        ];
    }
}