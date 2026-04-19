<?php

namespace App\Filament\Resources\PklPlacements\Schemas;

use Dotswan\MapPicker\Infolists\MapEntry; // <-- Komponen Map khusus untuk Infolist
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PklPlacementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs Penempatan Detail')
                    ->tabs([
                        // TAB 1: DATA UTAMA
                        Tab::make('Data Utama')
                            ->icon('heroicon-m-user-group')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextEntry::make('academicYear.name')->label('Tahun Ajaran')->badge()->color('success'),
                                    TextEntry::make('status')
                                        ->label('Status')
                                        ->badge()
                                        ->color(fn(string $state): string => match ($state) {
                                            'Aktif' => 'success',
                                            'Ditarik' => 'danger',
                                            default => 'gray',
                                        }),
                                    TextEntry::make('student.name')->label('Nama Siswa'),
                                    TextEntry::make('student.nis')->label('NIS Siswa'),
                                    TextEntry::make('dudika.name')->label('Tempat PKL (DUDIKA)'),
                                    TextEntry::make('teacher.name')->label('Guru Pembimbing'),
                                ]),
                            ]),

                        // TAB 2: WAKTU PELAKSANAAN & LOKASI
                        Tab::make('Waktu & Lokasi')
                            ->icon('heroicon-m-calendar-days')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextEntry::make('start_date')->label('Tanggal Mulai')->date('d F Y'),
                                    TextEntry::make('end_date')->label('Tanggal Selesai')->date('d F Y'),
                                    TextEntry::make('pkl_field')->label('Bidang Pekerjaan')->placeholder('-'),
                                ]),

                                MapEntry::make('location')
                                    ->label('Peta Titik Absensi')
                                    ->columnSpanFull()
                                    ->visible(fn($record) => !empty($record->latitude) && !empty($record->longitude))
                                    ->state(fn($record) => [
                                        'lat' => (float) $record?->latitude,
                                        'lng' => (float) $record?->longitude,
                                    ])
                                    // ✅ Ganti ke OpenStreetMap (bebas CORS, tidak hitam)
                                    ->tilesUrl('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')
                                    ->zoom(15) // ✅ Wajib agar map langsung fokus ke koordinat
                                    ->extraStyles(['min-height: 400px', 'border-radius: 12px'])
                                    ->showMarker(true)
                                    ->markerColor("#ef4444")
                                    ->showFullscreenControl(true)
                                    ->showZoomControl(true)
                                    ->draggable(false)
                                    ->clickable(false),

                                Grid::make(3)->schema([
                                    TextEntry::make('latitude')->label('Latitude')->placeholder('-'),
                                    TextEntry::make('longitude')->label('Longitude')->placeholder('-'),
                                    TextEntry::make('radius')->label('Radius Absensi')->formatStateUsing(fn($state) => "{$state} Meter")->placeholder('-'),
                                ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
