<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use App\Models\Identitas;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Filament\Navigation\NavigationItem;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Widgets\DashboardOwnerStats;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()

            ->colors([
                //
            ])

            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )

            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )

            ->pages([
                Dashboard::class,
            ])

            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets'
            )

            ->widgets([
                DashboardOwnerStats::class,
            ])

            ->navigationItems([
                NavigationItem::make('Face ID Karyawan')
                    ->url('/owner/karyawan', shouldOpenInNewTab: false)
                    ->icon('heroicon-o-user-circle')
                    ->group('Absensi')
                    ->sort(1)
                    ->visible(fn() => optional(Auth::user())->role === 'owner'),

                NavigationItem::make('Rekap Absensi')
                    ->url('/owner/absensi', shouldOpenInNewTab: false)
                    ->icon('heroicon-o-clock')
                    ->group('Absensi')
                    ->sort(2)
                    ->visible(fn() => optional(Auth::user())->role === 'owner'),

                NavigationItem::make('Laporan Keuangan')
                    ->url('/admin/laporan-keuangan', shouldOpenInNewTab: false)
                    ->icon('heroicon-o-banknotes')
                    ->group('Laporan')
                    ->sort(3)
                    ->visible(fn() => optional(Auth::user())->role === 'owner'),
            ])
            ->brandLogo(function () {
                $identitas = Identitas::first();

                return $identitas?->logo
                    ? asset('storage/' . $identitas->logo)
                    : asset('images/default-logo.png');
            })
            ->brandLogoHeight('2rem')
            ->brandName('')
            ->renderHook(
                'panels::head.end',
                fn() => Blade::render(<<<'BLADE'
                    <script src="https://cdn.tailwindcss.com"></script>
                    <script>
                        tailwind.config = {
                            corePlugins: { preflight: false },
                            theme: {
                                extend: {
                                    colors: {
                                        brand: {
                                            red: '#C0271A',
                                            darkred: '#9B1E13',
                                            yellow: '#F5C518',
                                            cream: '#FFF8E7',
                                        }
                                    },
                                    fontFamily: {
                                        display: ['Bebas Neue', 'sans-serif'],
                                        body: ['Nunito', 'sans-serif'],
                                    }
                                }
                            }
                        }
                    </script>
                    <style>
                        :root {
                            --brand-red: #C0271A;
                            --brand-darkred: #9B1E13;
                            --brand-yellow: #F5C518;
                            --brand-cream: #FFF8E7;
                        }

                        body {
                            font-family: "Nunito", sans-serif !important;
                        }

                        .fi-main,
                        .fi-body {
                            background-color: var(--brand-cream) !important;
                        }

                        .fi-sidebar {
                            background: #ffffff !important;
                            border-right: 2px solid #f3f4f6 !important;
                        }

                        .fi-sidebar-header {
                            background: var(--brand-red) !important;
                            padding: 2rem 0 !important;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                        }

                        .fi-sidebar-item-button {
                            border-radius: 12px !important;
                            font-weight: 700 !important;
                            font-size: .875rem !important;
                            transition: all .2s ease !important;
                        }

                        .fi-sidebar-item-button:hover {
                            background: var(--brand-cream) !important;
                            color: var(--brand-red) !important;
                        }

                        .fi-sidebar-item-button[aria-current="page"],
                        .fi-sidebar-item-button.fi-active {
                            background: var(--brand-red) !important;
                            color: #ffffff !important;
                            box-shadow: 0 4px 12px rgba(192, 39, 26, .25) !important;
                        }
                        


                        .fi-topbar {
                            border-bottom: 4px solid var(--brand-red) !important;
                            background: #ffffff !important;
                        }

                        .fi-page-header-heading {
                            font-family: "Bebas Neue", sans-serif !important;
                            color: var(--brand-red) !important;
                            font-size: 3rem !important;
                            letter-spacing: .05em !important;
                        }

                        .fi-ta-header-cell {
                            font-family: "Bebas Neue", sans-serif !important;
                            color: var(--brand-red) !important;
                            letter-spacing: .08em !important;
                            font-size: .9rem !important;
                        }

                        .fi-ta-row td {
                            transition: background-color .2s ease !important;
                        }

                        .fi-ta-row:hover td {
                            background: #FEF2F2 !important;
                        }

                        .fi-btn-primary {
                            background: var(--brand-red) !important;
                            border-radius: 12px !important;
                            font-weight: 700 !important;
                        }

                        .fi-btn-primary:hover {
                            background: var(--brand-darkred) !important;
                        }

                        .fi-section-header-heading {
                            font-family: "Bebas Neue", sans-serif !important;
                            color: var(--brand-red) !important;
                            letter-spacing: .05em !important;
                        }

                        .fi-section {
                            border: 1.5px solid #f0e6d3 !important;
                            border-radius: 14px !important;
                            box-shadow: 0 2px 10px rgba(0, 0, 0, .05) !important;
                        }

                        .fi-breadcrumbs li,
                        .fi-breadcrumbs a {
                            color: #9ca3af !important;
                            font-size: .8rem !important;
                            font-weight: 600 !important;
                        }

                        .fi-breadcrumbs li:last-child {
                            color: var(--brand-red) !important;
                        }

                        ::-webkit-scrollbar {
                            width: 5px;
                        }

                        ::-webkit-scrollbar-thumb {
                            background: #e2c4c4;
                            border-radius: 99px;
                        }
                    </style>
                BLADE)
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
