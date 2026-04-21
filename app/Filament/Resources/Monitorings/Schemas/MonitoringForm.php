<?php

namespace App\Filament\Resources\Monitorings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use App\Models\MonitoringSchedule;
use App\Models\PklPlacement;
use Filament\Schemas\Components\Grid;

class MonitoringForm
{
    public static function configure(Schema $schema): Schema
    {
        // KITA CARI DULU JADWALNYA DI SINI BIAR $activeSchedule TIDAK ERROR
        $activeSchedule = MonitoringSchedule::where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();

        return $schema->components([
            Grid::make(2)
                ->columnSpanFull()
                ->schema([
                    // Sembunyikan ID jadwal
                    Hidden::make('monitoring_schedule_id')
                        ->default($activeSchedule?->id),

                    // Activity terisi otomatis dan dikunci (Sesuai kesepakatan)
                    TextInput::make('activity')
                        ->label('Nama Kegiatan Monitoring')
                        ->default($activeSchedule?->name)
                        ->readOnly()
                        ->columnSpanFull()
                        ->helperText('Diisi otomatis sesuai jadwal dari Humas.'),

                    // Pilihan Siswa & DUDIKA (Menggunakan pkl_placement_id sesuai ide cerdasmu!)
                    Select::make('pkl_placement_id')
                        ->label('Siswa & DUDIKA')
                        ->options(function () {
                            return PklPlacement::with(['student', 'dudika'])->get()->mapWithKeys(function ($placement) {
                                return [$placement->id => $placement->student->name . ' - ' . $placement->dudika->name];
                            });
                        })
                        ->searchable()
                        ->required()
                        ->columnSpanFull()
                        ->helperText('Pilih siswa yang sedang dimonitoring.'),

                    DatePicker::make('date')
                        ->label('Tanggal Kunjungan')
                        ->default(now())
                        ->required(),

                    TimePicker::make('time')
                        ->label('Waktu Kunjungan')
                        ->default(now())
                        ->required(),

                    FileUpload::make('photo_path')
                        ->label('Foto Bukti Kunjungan')
                        ->image()
                        ->imageEditor() // Tambahkan ini biar guru bisa nge-crop/rotate foto
                        ->disk('public')
                        ->directory('monitorings')
                        ->imageResizeMode('cover') // Otomatis kompres biar server nggak penuh
                        ->imageResizeTargetWidth('1024')
                        ->imageResizeTargetHeight('1024')
                        ->required()
                        ->columnSpanFull()
                        ->helperText('Unggah foto bersama pihak DUDIKA. Anda dapat mengambil dari galeri.'),
                ])
        ]);
    }
}
