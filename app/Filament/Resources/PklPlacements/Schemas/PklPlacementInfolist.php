<?php

namespace App\Filament\Resources\PklPlacements\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
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

                                // ✅ Pakai ViewEntry + blade yang sama persis
                                ViewEntry::make('location_map')
                                    ->label('Peta Titik Absensi')
                                    ->view('filament.infolists.map-readonly')
                                    ->columnSpanFull()
                                    ->visible(fn($record) => !empty($record->latitude) && !empty($record->longitude)),

                                Grid::make(3)->schema([
                                    TextEntry::make('latitude')->label('Latitude')->placeholder('-'),
                                    TextEntry::make('longitude')->label('Longitude')->placeholder('-'),
                                    TextEntry::make('radius')
                                        ->label('Radius Absensi')
                                        ->formatStateUsing(fn($state) => "{$state} Meter")
                                        ->placeholder('-'),
                                ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
