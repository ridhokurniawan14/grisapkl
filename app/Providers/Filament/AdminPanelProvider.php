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
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;
use App\Models\SchoolProfile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;
// ==========================================
// MANTRA SAKTI 1: Tambahkan Hook & Route!
// ==========================================
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Route;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $brandHtml = config('app.name', 'Grisa PKL');
        $brandHeight = '2rem';
        $faviconUrl = null; // Siapkan variabel penampung Favicon

        try {
            if (Schema::hasTable('school_profiles')) {
                $profile = SchoolProfile::first();

                if ($profile && $profile->logo_path) {
                    $logoUrl = asset('storage/' . $profile->logo_path);
                    $appName = $profile->name ?? config('app.name', 'Grisa PKL');

                    // Set Favicon otomatis dari database!
                    $faviconUrl = $logoUrl;

                    $brandHtml = new HtmlString('
                        <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                            <img src="' . $logoUrl . '" alt="Logo Sekolah" style="height: 3rem; width: auto; object-fit: contain;">
                            <span style="font-weight: bold; font-size: 1.5rem; letter-spacing: -0.025em;">' . $appName . '</span>
                        </div>
                    ');

                    $brandHeight = '3.5rem';
                }
            }
        } catch (\Exception $e) {
            // Abaikan jika database belum siap
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->brandLogo($brandHtml)
            ->brandLogoHeight($brandHeight)
            // ==============================================================
            // MANTRA SAKTI 2: PASANG FAVICON KE TAB BROWSER!
            // ==============================================================
            ->favicon($faviconUrl)
            ->profile()
            ->navigationGroups([
                NavigationGroup::make()->label('Data Master'),
                NavigationGroup::make()->label('Transaksi PKL'),
                NavigationGroup::make()->label('Pencetakan'),
                NavigationGroup::make()->label('Sistem'),
                NavigationGroup::make()->label('Filament Shield'),
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('5s')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                \App\Filament\Widgets\LatestActivityLog::class,
                \App\Filament\Widgets\TeacherMonitoringRecap::class,
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
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            // ==============================================================
            // MANTRA SAKTI 3: SULAP HALAMAN LOGIN JADI MODERN & MEWAH!
            // ==============================================================
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                function (): string {
                    // Pastikan desain ini HANYA muncul di halaman Login (bukan di Dasbor)
                    if (request()->routeIs('filament.admin.auth.login')) {
                        return new HtmlString('
                            <style>
                                /* Ganti URL gambar di bawah ini dengan foto gerbang/gedung SMK PGRI 1 Giri nantinya! */
                                .fi-simple-layout {
                                    background-image: url("https://smkpgri1giri.sch.id/storage/settings/banner_1761022031_eg0vkQ.jpg");
                                    background-size: cover;
                                    background-position: center;
                                    background-repeat: no-repeat;
                                    background-attachment: fixed;
                                }
                                /* Overlay gelap elegan ala Netflix/Startup agar box form tetap terbaca jelas */
                                .fi-simple-layout::before {
                                    content: "";
                                    position: absolute;
                                    inset: 0;
                                    background: rgba(15, 23, 42, 0.65); /* Warna slate gelap dengan efek transparan */
                                    z-index: 0;
                                }
                                /* Pastikan Box Login ada di depan layar (tidak tertutup overlay) */
                                .fi-simple-main-ctn {
                                    position: relative;
                                    z-index: 1;
                                }
                                /* Box Shadow Premium & Ujung Membulat untuk Card Login */
                                .fi-simple-main-ctn > section {
                                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
                                    border-radius: 1.5rem !important; 
                                    border: 1px solid rgba(255, 255, 255, 0.15);
                                }
                            </style>
                        ');
                    }
                    return '';
                }
            );
    }
}
