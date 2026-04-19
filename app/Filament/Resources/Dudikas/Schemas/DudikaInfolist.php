<?php

namespace App\Filament\Resources\Dudikas\Schemas;

use Dotswan\MapPicker\Infolists\MapEntry; // <-- Komponen Map khusus untuk Infolist
use Filament\Infolists\Components\TextEntry;
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
                        // TAB 1: PROFIL & KONTAK (GABUNGAN)
                        Tab::make('Profil & Kontak')
                            ->icon('heroicon-m-building-office-2')
                            ->schema([
                                TextEntry::make('name')->label('Nama Instansi / DUDIKA'),
                                TextEntry::make('address')->label('Alamat Lengkap')->placeholder('-')->columnSpanFull(),

                                Grid::make(2)->schema([
                                    TextEntry::make('head_name')->label('Nama Pimpinan / Direktur')->icon('heroicon-m-user')->placeholder('-'),
                                    TextEntry::make('head_nip')->label('NIP / NIK Pimpinan')->placeholder('-'),
                                ]),

                                \Filament\Infolists\Components\TextEntry::make('pembatas_supervisor')
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

                        // TAB 2: LOKASI ABSENSI (MAP READONLY)
                        Tab::make('Lokasi Absensi')
                            ->icon('heroicon-m-map-pin')
                            ->schema([
                                MapEntry::make('location')
                                    ->label('Peta Titik Absensi')
                                    ->columnSpanFull()
                                    ->visible(fn($record) => !empty($record->latitude) && !empty($record->longitude))
                                    ->state(fn($record) => [
                                        'lat' => (float) $record?->latitude,
                                        'lng' => (float) $record?->longitude,
                                    ])
                                    // ✅ Ganti ke OpenStreetMap
                                    ->tilesUrl('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')
                                    ->zoom(15) // ✅ Wajib!
                                    ->extraStyles(['min-height: 400px', 'border-radius: 12px'])
                                    ->showMarker(true)
                                    ->markerColor("#ef4444")
                                    ->showFullscreenControl(true)
                                    ->showZoomControl(true)
                                    ->draggable(false)
                                    ->clickable(false),

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
