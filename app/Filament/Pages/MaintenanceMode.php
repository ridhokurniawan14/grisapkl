<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class MaintenanceMode extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::WrenchScrewdriver;

    protected string $view = 'filament.pages.maintenance-mode';

    protected static string | \UnitEnum | null $navigationGroup = 'Sistem';
    protected static ?string $navigationLabel = 'Mode Pemeliharaan';
    protected static ?string $title = 'Manajemen Mode Pemeliharaan';
    protected static ?int $navigationSort = 4;

    protected function getHeaderActions(): array
    {
        $isDown = app()->isDownForMaintenance();

        return [
            Action::make('toggle_maintenance')
                ->label($isDown ? 'Matikan Mode Maintenance' : 'Aktifkan Mode Maintenance')
                ->icon($isDown ? 'heroicon-o-check-circle' : 'heroicon-o-no-symbol')
                ->color($isDown ? 'success' : 'danger')
                ->form(
                    // Jika sudah down, tidak perlu form. Jika belum down, munculkan form rahasia!
                    $isDown ? [] : [
                        TextInput::make('secret')
                            ->label('URL Jalur Rahasia (Bypass Token)')
                            ->default('grisa-admin')
                            ->required()
                            ->alphaDash() // Hanya boleh huruf, angka, strip, underscore (tanpa spasi)
                            ->helperText('Tentukan kata kunci tanpa spasi. Nantinya URL masuk akan menjadi: namawebsite.com/kata-kunci-ini')
                    ]
                )
                ->modalHeading($isDown ? 'Sistem Sudah Selesai Diperbaiki?' : 'Aktifkan Pemeliharaan Sistem?')
                ->action(function (array $data) use ($isDown) {
                    if ($isDown) {
                        Artisan::call('up');
                        Notification::make()
                            ->title('Sistem Kembali Online!')
                            ->body('Aplikasi sudah dibuka untuk publik.')
                            ->success()
                            ->send();
                    } else {
                        // Ambil kata rahasia dari form yang diinput user
                        $secret = $data['secret'] ?? 'grisa-admin';
                        Artisan::call('down', ['--secret' => $secret]);
                        Notification::make()
                            ->title('Maintenance Diaktifkan!')
                            ->body('Sistem offline. Ingat URL rahasia Anda: /' . $secret)
                            ->danger()
                            ->send();
                    }

                    return redirect(request()->header('Referer'));
                }),
        ];
    }

    // MANTRA SAKTI: Lempar data status & kata rahasia ke halaman Blade
    protected function getViewData(): array
    {
        $isDown = app()->isDownForMaintenance();
        $secret = null;

        // Baca file 'down' buatan Laravel untuk melihat apa password rahasia yang sedang aktif
        if ($isDown && file_exists(storage_path('framework/down'))) {
            $downData = json_decode(file_get_contents(storage_path('framework/down')), true);
            $secret = $downData['secret'] ?? null;
        }

        return [
            'isDown' => $isDown,
            'secret' => $secret,
        ];
    }
}
