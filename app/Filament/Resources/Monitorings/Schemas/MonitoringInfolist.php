<?php

namespace App\Filament\Resources\Monitorings\Schemas;

use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class MonitoringInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ==========================================
                // BARIS 1: IDENTITAS KUNJUNGAN (CARD)
                // ==========================================
                Section::make('Informasi Kunjungan Monitoring')
                    ->icon('heroicon-m-information-circle')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('pklPlacement.student.name')
                                ->label('Nama Siswa')
                                ->weight('bold')
                                ->color('primary')
                                ->placeholder('Data tidak ditemukan'),

                            TextEntry::make('pklPlacement.teacher.name')
                                ->label('Guru Pembimbing')
                                ->weight('bold')
                                ->placeholder('Data tidak ditemukan'),

                            TextEntry::make('pklPlacement.dudika.name')
                                ->label('Tempat PKL (DUDIKA)')
                                ->placeholder('Data tidak ditemukan'),

                            TextEntry::make('date')
                                ->label('Tanggal Kunjungan')
                                ->date('l, d F Y'),

                            TextEntry::make('time')
                                ->label('Waktu / Jam')
                                ->time('H:i \W\I\B'),
                        ]),
                    ]),

                // ==========================================
                // BARIS 2: DESKRIPSI & FOTO BUKTI (CARD)
                // ==========================================
                Section::make('Laporan Kegiatan')
                    ->icon('heroicon-m-document-text')
                    ->schema([
                        TextEntry::make('activity')
                            ->label('Deskripsi Kegiatan / Hasil Monitoring')
                            ->columnSpanFull()
                            ->prose(), // Biar teks panjang rapi berparagraf

                        // BAGIAN FOTO DENGAN ZOOM MODAL SAKTI
                        ImageEntry::make('photo_path')
                            ->label('Foto Bukti Kunjungan')
                            ->columnSpanFull()
                            ->disk('public')
                            ->extraImgAttributes([
                                'class' => 'rounded-xl shadow-md cursor-pointer hover:opacity-80 transition-opacity',
                                'style' => 'height: 400px; width: 100%; max-width: 600px; object-fit: cover;'
                            ])
                            ->action(
                                Action::make('zoom_foto')
                                    ->modalHeading('Detail Foto Kunjungan')
                                    ->modalWidth('5xl')
                                    ->modalSubmitAction(false)
                                    ->modalCancelActionLabel('Tutup')
                                    ->modalContent(fn($record) => new HtmlString('
                                        <div style="display: flex; justify-content: center; align-items: center;">
                                            <img src="' . Storage::disk('public')->url($record->photo_path) . '" alt="Foto Kunjungan" style="max-width: 100%; max-height: 75vh; object-fit: contain; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
                                        </div>
                                    '))
                            ),
                    ]),
            ]);
    }
}
