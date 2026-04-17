<?php

namespace App\Filament\Resources\PklPlacements\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PklPlacementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Penempatan')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('academicYear.name')->label('Tahun Ajaran')->badge()->color('success'),
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->color(fn(string $state): string => match ($state) {
                                    'Aktif' => 'success',
                                    'Ditarik' => 'danger',
                                }),
                            TextEntry::make('student.name')->label('Nama Siswa'),
                            TextEntry::make('student.nis')->label('NIS Siswa'),
                            TextEntry::make('dudika.name')->label('Tempat PKL (DUDIKA)'),
                            TextEntry::make('teacher.name')->label('Guru Pembimbing'),
                        ]),
                    ]),

                Section::make('Waktu Pelaksanaan & Lokasi')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('start_date')->label('Tanggal Mulai')->date('d F Y'),
                            TextEntry::make('end_date')->label('Tanggal Selesai')->date('d F Y'),
                            TextEntry::make('pkl_field')->label('Bidang Pekerjaan')->placeholder('-'),

                            TextEntry::make('latitude')->label('Latitude')->placeholder('-'),
                            TextEntry::make('longitude')->label('Longitude')->placeholder('-'),
                            TextEntry::make('radius')->label('Radius Absensi')->formatStateUsing(fn($state) => "{$state} Meter")->placeholder('-'),
                        ]),
                    ]),
            ]);
    }
}
