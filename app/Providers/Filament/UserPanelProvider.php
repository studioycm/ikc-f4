<?php

namespace App\Providers\Filament;

use App\Filament\User\Pages\BreedingActivityDashboard;
use App\Filament\User\Pages\Dashboard as UserDashboard;
use App\Filament\User\Pages\DogsDashboard;
use App\Filament\User\Pages\MembershipsDashboard;
use App\Filament\User\Pages\PaymentsDashboard;
use App\Filament\User\Pages\RequestsDashboard;
use App\Filament\User\Pages\ShowsDashboard;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user')
            ->path('user')
            ->login()
            ->profile()
            ->passwordReset()
            ->emailVerification()
            ->authGuard('web')
            ->databaseNotifications()
            ->maxContentWidth(Width::Full)
            ->breadcrumbs(true)
            ->brandName(__('IKC System'))
            ->favicon(asset('favicon.png'))
            ->brandLogo(asset('images/logo-light.svg'))
            ->darkModeBrandLogo(asset('images/logo-dark.svg'))
            ->brandLogoHeight('3rem')
            ->font('Assistant', provider: GoogleFontProvider::class)
            ->sidebarWidth('12rem')
            ->sidebarCollapsibleOnDesktop()
            ->collapsedSidebarWidth('5rem')
//            ->topNavigation()
            ->colors([
                'primary' => Color::Amber,
                'pink' => Color::Pink,
                'purple' => Color::Purple,
                'indigo' => Color::Indigo,
                'blue' => Color::Blue,
                'green' => Color::Green,
                'yellow' => Color::Yellow,
                'orange' => Color::Orange,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/User/Resources'), for: 'App\\Filament\\User\\Resources')
            ->discoverPages(in: app_path('Filament/User/Pages'), for: 'App\\Filament\\User\\Pages')
            ->pages([
                UserDashboard::class,
                DogsDashboard::class,
                MembershipsDashboard::class,
                RequestsDashboard::class,
                PaymentsDashboard::class,
                BreedingActivityDashboard::class,
                ShowsDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/User/Widgets'), for: 'App\\Filament\\User\\Widgets')
            ->widgets([
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
            ])
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): View => view('filament.auth.login-panel-artwork', [
                    'displayImage' => false,
                    'panelName' => null,
                    'imageUrl' => asset('images/logo-light.svg'),
                    'darkImageUrl' => asset('images/logo-dark.svg'),
                    'imageAlt' => __('IKC System user panel logo'),
                    'heading' => __('IKC System') . " | " . __('User Panel'),
                    'description' => __('Member workspace for dogs, memberships, requests, payments, and shows'),
                   // 'switchUrl' => null,
                   // 'switchLabel' => null,
                ]),
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER, function () {
                    $prev_user = auth()->user()->prevUser;

                    return Blade::render('filament.user.components.prev-user-badge',
                        [
                            'color' => 'primary',
                            'icon' => 'fas-user',
                            'prev_user_name' => $prev_user->name,
                            'prev_user_id' => $prev_user->id,
                            'prev_user_phone' => $prev_user->normalised_phone,
                        ]);
                }
            );
    }
}
