<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Blade;

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
                // 'primary' => Color::hex('#C0271A'),
                // 'gray'    => Color::Zinc,
            ])
            ->font(
                'Nunito',
                'https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Bebas+Neue&display=swap'
            )
            ->brandName('Tahu Bakso Morojoyo')
            // ->brandLogo(fn () => view('filament.brand-logo'))
            ->brandLogoHeight('2.5rem')
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups(['Utama', 'Data Master'])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->renderHook(
                'panels::head.end',
                fn () => Blade::render('
                    <script src="https://cdn.tailwindcss.com"></script>
                    <script>
                        tailwind.config = {
                            corePlugins: { preflight: false },
                            theme: {
                                extend: {
                                    colors: {
                                        brand: {
                                            red:     "#C0271A",
                                            darkred: "#9B1E13",
                                            yellow:  "#F5C518",
                                            cream:   "#FFF8E7",
                                        }
                                    },
                                    fontFamily: {
                                        display: ["Bebas Neue", "sans-serif"],
                                        body:    ["Nunito", "sans-serif"],
                                    }
                                }
                            }
                        }
                    </script>
                    <style>
                        /* ══ DARK MODE — kembalikan ke default Filament ══ */
                        html.dark body,
                        html.dark .fi-body,
                        html.dark .fi-main,
                        html.dark main,
                        html.dark .fi-main-ctn { background-color: unset !important; color: unset !important; }

                        html.dark .fi-topbar,
                        html.dark .fi-topbar nav { background-color: unset !important; border-bottom: unset !important; box-shadow: unset !important; }

                        html.dark .fi-sidebar,
                        html.dark aside { background-color: unset !important; border-right: unset !important; }

                        html.dark .fi-sidebar-header { background: unset !important; border-bottom: unset !important; }
                        html.dark .fi-sidebar-header * { color: unset !important; font-family: unset !important; }

                        html.dark .fi-sidebar-item-button { background: unset !important; color: unset !important; border-left: unset !important; box-shadow: unset !important; }
                        html.dark .fi-sidebar-item-button:hover { background: unset !important; color: unset !important; border-left-color: unset !important; }
                        html.dark .fi-sidebar-item-button[aria-current="page"],
                        html.dark .fi-sidebar-item-button.fi-active { background: unset !important; color: unset !important; box-shadow: unset !important; }
                        html.dark .fi-sidebar-item-button svg { color: unset !important; }

                        html.dark .fi-page-header-heading { color: unset !important; font-size: unset !important; }
                        html.dark .fi-page-header-heading::after { display: none !important; }

                        html.dark .fi-ta-header-cell { background: unset !important; color: unset !important; border-bottom: unset !important; font-family: unset !important; }
                        html.dark .fi-ta-ctn { border: unset !important; box-shadow: unset !important; }
                        html.dark .fi-ta-row:hover td { background: unset !important; }
                        html.dark .fi-ta-row td { border-bottom: unset !important; color: unset !important; }

                        html.dark .fi-btn-primary { background: unset !important; box-shadow: unset !important; border: unset !important; }
                        html.dark .fi-btn-primary:hover { background: unset !important; transform: unset !important; }

                        html.dark .fi-section { border: unset !important; box-shadow: unset !important; }
                        html.dark .fi-section-header-heading { color: unset !important; }

                        html.dark .fi-sidebar-group-label { color: unset !important; }
                        html.dark .fi-breadcrumbs li,
                        html.dark .fi-breadcrumbs a { color: unset !important; }
                        html.dark .fi-breadcrumbs li:last-child { color: unset !important; }

                        html.dark input,
                        html.dark textarea,
                        html.dark select { background-color: unset !important; border-color: unset !important; color: unset !important; }
                        html.dark input:focus { border-color: unset !important; box-shadow: unset !important; }

                        html.dark ::-webkit-scrollbar-track { background: unset; }
                        html.dark ::-webkit-scrollbar-thumb { background: unset; }

                        :root {
                            --brand-red:     #C0271A;
                            --brand-darkred: #9B1E13;
                            --brand-yellow:  #F5C518;
                            --brand-cream:   #FFF8E7;
                        }

                        body {
                            font-family: "Nunito", sans-serif !important;
                        }

                        .fi-main,
                        .fi-body {
                            background-color: var(--brand-cream) !important;
                        }

                        .fi-sidebar {
                            background: #ffffff;
                            border-right: 2px solid #f3f4f6;
                        }

                        .fi-sidebar-header {
                            background: var(--brand-red) !important;
                        }

                        .fi-sidebar-item-button {
                            border-radius: 12px !important;
                            font-weight: 700 !important;
                            font-size: .875rem !important;
                            transition: all .2s !important;
                        }

                        .fi-sidebar-item-button:hover {
                            background: var(--brand-cream) !important;
                            color: var(--brand-red) !important;
                        }

                        .fi-sidebar-item-button[aria-current="page"],
                        .fi-sidebar-item-button.fi-active {
                            background: var(--brand-red) !important;
                            color: #ffffff !important;
                            box-shadow: 0 4px 12px rgba(192,39,26,.25) !important;
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

                        .fi-wi {
                            background: transparent !important;
                            border: none !important;
                            box-shadow: none !important;
                            padding: 0 !important;
                        }

                        .fi-ta-header-cell {
                            font-family: "Bebas Neue", sans-serif !important;
                            color: var(--brand-red) !important;
                            letter-spacing: .08em !important;
                            font-size: .9rem !important;
                        }

                        .fi-ta-row td {
                            transition: background-color .2s ease;
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
                            box-shadow: 0 2px 10px rgba(0,0,0,.05) !important;
                        }

                        .fi-breadcrumbs li,
                        .fi-breadcrumbs a {
                            color: #9ca3af !important;
                            font-size: .8rem !important;
                            font-weight: 600 !important;
                        }

                        .fi-breadcrumbs li:last-child {
                            color: #C0271A !important;
                        }

                        ::-webkit-scrollbar {
                            width: 5px;
                        }

                        ::-webkit-scrollbar-thumb {
                            background: #e2c4c4;
                            border-radius: 99px;
                        }
                    </style>
                ')
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
            ->authMiddleware([Authenticate::class]);
    }
}