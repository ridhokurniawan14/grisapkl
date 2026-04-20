<?php

namespace App\Filament\Resources\Journals\Schemas;

use Dotswan\MapPicker\Infolists\MapEntry; // Komponen peta yang sama seperti di DUDIKA
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JournalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([

                    // KOLOM KIRI (Memakan 2 ruang)
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Identitas Siswa & PKL')
                            ->icon('heroicon-m-user')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextEntry::make('pklPlacement.student.name')
                                        ->label('Nama Siswa')
                                        ->weight('bold')
                                        ->color('primary')
                                        ->placeholder('Data tidak ditemukan'),

                                    TextEntry::make('pklPlacement.dudika.name')
                                        ->label('Tempat PKL / DUDIKA')
                                        ->placeholder('Data tidak ditemukan'),

                                    TextEntry::make('pklPlacement.student.studentClass.name')
                                        ->label('Kelas Siswa')
                                        ->placeholder('-'),

                                    TextEntry::make('pklPlacement.teacher.name')
                                        ->label('Guru Pembimbing')
                                        ->placeholder('-'),
                                ]),
                            ]),

                        Section::make('Laporan Kegiatan')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextEntry::make('date')
                                        ->label('Tanggal')
                                        ->date('l, d F Y'),
                                    TextEntry::make('time')
                                        ->label('Waktu Absen')
                                        ->time('H:i \W\I\B'),
                                    TextEntry::make('attend_status')
                                        ->label('Status Kehadiran')
                                        ->badge()
                                        ->color(fn(string $state): string => match ($state) {
                                            'Hadir' => 'success',
                                            'Izin' => 'warning',
                                            'Sakit' => 'danger',
                                            default => 'gray',
                                        }),
                                ]),

                                TextEntry::make('activity')
                                    ->label('Deskripsi Pekerjaan / Kegiatan')
                                    ->columnSpanFull()
                                    ->prose(),

                                ImageEntry::make('photo_path')
                                    ->label('Foto Bukti Kegiatan')
                                    ->columnSpanFull()
                                    ->height(400)
                                    ->extraImgAttributes(['class' => 'rounded-xl shadow-md']),
                            ]),
                    ]),

                    // KOLOM KANAN (Memakan 1 ruang)
                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('Status Validasi DUDIKA')
                            ->icon('heroicon-m-check-badge')
                            ->schema([
                                IconEntry::make('is_valid')
                                    ->label('Disetujui?')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-badge')
                                    ->falseIcon('heroicon-o-x-circle'),

                                TextEntry::make('revision_note')
                                    ->label('Catatan Revisi')
                                    ->placeholder('Tidak ada catatan / Jurnal Valid.')
                                    ->color('danger'),
                            ]),
                    ]),
                ]),

                // SECTION LOKASI DIKELUARKAN DARI GRID AGAR FULL WIDTH (LEBAR PENUH)
                Section::make('Lokasi Saat Absen')
                    ->icon('heroicon-m-map-pin')
                    ->columnSpanFull()
                    ->schema([
                        MapEntry::make('location')
                            ->label('Peta Titik Absen')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->visible(fn($record) => !empty($record->latitude) && !empty($record->longitude))
                            ->state(fn($record) => [
                                'lat' => (float) $record?->latitude,
                                'lng' => (float) $record?->longitude,
                            ])
                            ->tilesUrl('https://mt0.google.com/vt/lyrs=y&hl=en&x={x}&y={y}&z={z}')
                            ->extraStyles(['min-height: 400px', 'border-radius: 12px']) // Tinggikan jadi 400px biar mantap
                            ->showMarker(true)
                            ->markerColor("#3b82f6")
                            ->draggable(false)
                            ->showZoomControl(true)
                            ->clickable(false),
                    ]),
            ]);
    }
}
