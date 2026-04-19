<?php

namespace App\Filament\Resources\Dudikas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry; // ← Ganti import ini
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class DudikaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs Dudika Detail')
                    ->tabs([
                        // TAB 1: PROFIL & KONTAK
                        Tab::make('Profil & Kontak')
                            ->icon('heroicon-m-building-office-2')
                            ->schema([
                                TextEntry::make('name')->label('Nama Instansi / DUDIKA'),
                                TextEntry::make('address')->label('Alamat Lengkap')->placeholder('-')->columnSpanFull(),

                                Grid::make(2)->schema([
                                    TextEntry::make('head_name')->label('Nama Pimpinan / Direktur')->icon('heroicon-m-user')->placeholder('-'),
                                    TextEntry::make('head_nip')->label('NIP / NIK Pimpinan')->placeholder('-'),
                                ]),

                                TextEntry::make('pembatas_supervisor')
                                    ->label('Data Pembimbing Lapangan (Instruktur)')
                                    ->default('')
                                    ->helperText('Orang yang akan membimbing dan menilai siswa di lokasi.')
                                    ->columnSpanFull(),

                                Grid::make(3)->schema([
                                    TextEntry::make('supervisor_name')->label('Nama Pembimbing')->icon('heroicon-m-user-group')->placeholder('-'),
                                    TextEntry::make('supervisor_nip')->label('NIP / NIK')->placeholder('-'),
                                    TextEntry::make('supervisor_phone')
                                        ->label('No. HP / WA')
                                        ->icon('heroicon-m-phone')
                                        ->color('success')
                                        ->url(function ($state) {
                                            if (blank($state)) return null;
                                            $phone = preg_replace('/[^0-9]/', '', $state);
                                            if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
                                            return "https://wa.me/{$phone}";
                                        })
                                        ->openUrlInNewTab()
                                        ->placeholder('-'),
                                ]),
                            ]),

                        // TAB 2: LOKASI ABSENSI
                        Tab::make('Lokasi Absensi')
                            ->icon('heroicon-m-map-pin')
                            ->schema([
                                // ✅ PAKAI ViewEntry — koordinat langsung dari $record, tidak bergantung dotswan lifecycle
                                ViewEntry::make('location_map')
                                    ->label('Peta Titik Absensi')
                                    ->view('filament.infolists.map-readonly')
                                    ->columnSpanFull()
                                    ->visible(fn($record) => !empty($record->latitude) && !empty($record->longitude)),

                                Grid::make(3)->schema([
                                    TextEntry::make('latitude')
                                        ->label('Latitude')
                                        ->placeholder('Belum diatur'),
                                    TextEntry::make('longitude')
                                        ->label('Longitude')
                                        ->placeholder('Belum diatur'),
                                    TextEntry::make('radius')
                                        ->label('Radius Absensi')
                                        ->formatStateUsing(fn($state) => "{$state} Meter")
                                        ->placeholder('Belum diatur'),
                                ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
