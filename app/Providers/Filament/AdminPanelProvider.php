<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider as SocialProvider;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->passwordReset()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->navigationItems([
                NavigationItem::make('Log Viewer')
                    ->group('Resource')
                    ->icon('heroicon-o-document-text')
                    ->url('/admin/log-viewer')
                    ->isActiveWhen(fn() => request()->is('admin/log-viewer*'))
                    ->visible(fn() => optional(auth()->user())->hasRole('super_admin')),
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
            ->plugins([
                FilamentShieldPlugin::make()
                    ->localizePermissionLabels(true)
                    ->simpleResourcePermissionView(false)
                    ->navigationGroup('Security')
                    ->gridColumns(1)
                    ->resourceCheckboxListColumns(['sm' => 2, 'lg' => 4]),
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        userMenuLabel: 'My Profile',
                        shouldRegisterNavigation: false,
                        hasAvatars: false,
                        slug: 'my-profile',
                    ),
                FilamentSocialitePlugin::make()
                    ->providers([
                        SocialProvider::make('google')->label('Google'),
                        SocialProvider::make('github')->label('GitHub'),
                    ])
                    ->registration(true)
                    ->createUserUsing(function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialitePlugin $plugin) {
                        return $plugin->getUserModelClass()::create([
                            'name' => $oauthUser->getName() ?: $oauthUser->getNickname() ?: $oauthUser->getEmail(),
                            'email' => $oauthUser->getEmail(),
                            'password' => bcrypt(str()->random(32)),
                        ]);
                    }),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
