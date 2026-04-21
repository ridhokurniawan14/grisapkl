<?php

namespace App\Filament\Resources\Journals\Schemas;

// PASTIKAN USE-NYA SEPERTI INI (KHUSUS INFOLISTS)

use Filament\Actions\Action;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class JournalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ==========================================
                // BARIS 1: IDENTITAS & VALIDASI (DIGABUNG 1 CARD)
                // ==========================================
                Section::make('Ringkasan Jurnal')
                    ->icon('heroicon-m-clipboard-document-check')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)->schema([

                            // BAGIAN KIRI: Identitas (Mengambil 2 Kolom)
                            Grid::make(2)->columnSpan(['default' => 3, 'md' => 2])->schema([
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

                            // BAGIAN KANAN: Validasi (Mengambil 1 Kolom) + Garis Pembatas Kiri
                            Grid::make(1)->columnSpan(['default' => 3, 'md' => 1])
                                ->extraAttributes(['class' => 'md:border-l md:pl-6 border-gray-200 dark:border-gray-700'])
                                ->schema([
                                    IconEntry::make('is_valid')
                                        ->label('Status Validasi DUDIKA')
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

                // ==========================================
                // BARIS 2: LAPORAN KEGIATAN
                // ==========================================
                Section::make('Laporan Kegiatan')
                    ->icon('heroicon-m-document-text')
                    ->columnSpanFull()
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

                        Grid::make(2)->schema([
                            // ==========================================
                            // 1. BAGIAN FOTO (Native Modal Filament + Auto URL)
                            // ==========================================
                            ImageEntry::make('photo_path')
                                ->label('Foto Bukti Kegiatan')
                                ->columnSpan(1)
                                ->disk('public')
                                ->extraImgAttributes([
                                    'class' => 'rounded-xl shadow-md cursor-pointer hover:opacity-80 transition-opacity',
                                    'style' => 'height: 400px; width: 100%; object-fit: cover;'
                                ])
                                ->action(
                                    // Panggil modal bawaan Filament yang anti-error
                                    Action::make('zoom_foto')
                                        ->modalHeading('Detail Foto Kegiatan')
                                        ->modalSubmitAction(false) // Sembunyikan tombol Submit
                                        ->modalCancelActionLabel('Tutup')
                                        ->modalContent(fn($record) => new HtmlString('
                                            <div style="display: flex; justify-content: center; align-items: center;">
                                                <img src="' . Storage::disk('public')->url($record->photo_path) . '" alt="Foto Kegiatan" style="max-width: 100%; max-height: 75vh; object-fit: contain; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
                                            </div>
                                        '))
                                ),

                            // ==========================================
                            // 2. BAGIAN PETA (Dibungkus Fieldset)
                            // ==========================================
                            Fieldset::make('Peta Titik Absensi')
                                ->columnSpan(1)
                                ->schema([
                                    ViewEntry::make('location_map')
                                        ->hiddenLabel()
                                        ->view('filament.infolists.map-readonly')
                                        ->columnSpanFull()
                                        ->visible(fn($record) => !empty($record->latitude) && !empty($record->longitude)),
                                ]),
                        ]),
                    ]),
            ]);
    }
}
