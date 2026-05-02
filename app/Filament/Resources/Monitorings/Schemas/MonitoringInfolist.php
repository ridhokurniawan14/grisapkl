<?php

namespace App\Filament\Resources\Monitorings\Schemas;

use App\Models\Monitoring;
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
                Section::make('Informasi Kunjungan Monitoring')
                    ->icon('heroicon-m-information-circle')
                    ->schema([
                        Grid::make(2)->schema([

                            TextEntry::make('students_in_visit')
                                ->label('Siswa yang Tercakup')
                                ->weight('bold')
                                ->color('primary')
                                ->columnSpanFull()
                                ->html() // ✅ Wajib biar <br> ke-render
                                ->state(function (Monitoring $record): string {
                                    $students = Monitoring::where('date', $record->date)
                                        ->where('monitoring_schedule_id', $record->monitoring_schedule_id)
                                        ->whereHas('pklPlacement', fn($q) => $q->where(
                                            'dudika_id',
                                            $record->pklPlacement->dudika_id
                                        ))
                                        ->with('pklPlacement.student')
                                        ->get()
                                        ->map(fn($m) => $m->pklPlacement?->student?->name)
                                        ->filter()
                                        ->values();

                                    if ($students->isEmpty()) {
                                        return 'Data tidak ditemukan';
                                    }

                                    return $students
                                        ->map(fn($name, $i) => ($i + 1) . '. ' . $name)
                                        ->implode('<br>'); // ✅ Ini sudah benar
                                })
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

                Section::make('Laporan Kegiatan')
                    ->icon('heroicon-m-document-text')
                    ->schema([
                        TextEntry::make('activity')
                            ->label('Deskripsi Kegiatan / Hasil Monitoring')
                            ->columnSpanFull()
                            ->prose(),

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
                                            <img src="' . Storage::disk('public')->url($record->photo_path) . '"
                                                 alt="Foto Kunjungan"
                                                 style="max-width: 100%; max-height: 75vh; object-fit: contain; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
                                        </div>
                                    '))
                            ),
                    ]),
            ]);
    }
}
