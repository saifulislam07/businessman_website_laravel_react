<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\LatestMessages;
use App\Filament\Widgets\QuickActions;
use App\Filament\Widgets\SiteStats;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Fayer Ahammed — Admin')
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('favicon.svg'))
            ->font('Inter')
            ->colors([
                // Navy tones from the public site, brightened so buttons and toggles read as
                // interactive (a single #0F1E36 palette rendered everything near-black).
                'primary' => [
                    50 => '238, 243, 250',
                    100 => '217, 227, 242',
                    200 => '181, 199, 227',
                    300 => '134, 161, 204',
                    400 => '87, 121, 176',
                    500 => '58, 94, 150',
                    600 => '36, 66, 112',
                    700 => '28, 52, 90',
                    800 => '21, 40, 70',
                    900 => '15, 30, 54',
                    950 => '10, 20, 36',
                ],
                'warning' => Color::hex('#C9A24B'),
                'gray' => Color::Slate,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups(['Content', 'Inbox', 'Settings'])
            ->userMenuItems([
                MenuItem::make()
                    ->label('View website')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (): string => config('portfolio.frontend_url'), shouldOpenInNewTab: true),
            ])
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => view('filament.admin-styles'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                QuickActions::class,
                SiteStats::class,
                LatestMessages::class,
            ])
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
