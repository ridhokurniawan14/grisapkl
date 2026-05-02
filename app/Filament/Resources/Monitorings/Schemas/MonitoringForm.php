<?php

namespace App\Filament\Resources\Monitorings\Schemas;

use App\Models\Dudika;
use App\Models\MonitoringSchedule;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class MonitoringForm
{
    public static function configure(Schema $schema): Schema
    {
        $activeSchedule = MonitoringSchedule::where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();

        return $schema->components([
            Grid::make(2)
                ->columnSpanFull()
                ->schema([
                    Select::make('monitoring_schedule_id')
                        ->label('Tahap Kunjungan (Jadwal)')
                        ->options(MonitoringSchedule::pluck('name', 'id'))
                        ->default($activeSchedule?->id)
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $schedule = MonitoringSchedule::find($state);
                            if ($schedule) {
                                $set('activity', $schedule->name);
                            }
                        }),

                    TextInput::make('activity')
                        ->label('Nama Kegiatan / Deskripsi')
                        ->default($activeSchedule?->name)
                        ->required(),

                    // ==============================================================
                    // MANTRA SAKTI 1: FIELD GURU PEMBIMBING (Otomatis deteksi login)
                    // ==============================================================
                    Select::make('teacher_id')
                        ->label('Guru Pembimbing')
                        ->options(\App\Models\Teacher::pluck('name', 'id'))
                        ->default(function () {
                            $user = auth()->user();
                            return $user && $user->teacher ? $user->teacher->id : null;
                        })
                        ->disabled(function () {
                            // Kunci field ini kalau yang login adalah guru
                            $user = auth()->user();
                            return $user && $user->teacher !== null;
                        })
                        ->dehydrated() // WAJIB ADA agar data tetap terkirim saat disubmit
                        ->required()
                        ->live() // Biar saat guru dipilih, DUDIKA-nya ikut berubah
                        ->afterStateUpdated(fn(callable $set) => $set('dudika_id', null)),

                    // ==============================================================
                    // MANTRA SAKTI 2: DUDIKA HANYA MUNCUL SESUAI GURU YANG DIPILIH
                    // ==============================================================
                    Select::make('dudika_id')
                        ->label('DUDIKA (Tempat PKL)')
                        ->options(function (callable $get) {
                            $teacherId = $get('teacher_id');

                            if (!$teacherId) return []; // Kosongkan kalau guru belum dipilih

                            return Dudika::whereHas('pklPlacements', function ($q) use ($teacherId) {
                                $q->whereHas('academicYear', fn($aq) => $aq->where('is_active', true))
                                    ->where('teacher_id', $teacherId); // Filter mutlak per guru
                            })->pluck('name', 'id');
                        })
                        ->searchable()
                        ->required()
                        ->afterStateHydrated(function ($state, $record, $set) {
                            if ($record?->pklPlacement) {
                                $set('dudika_id', $record->pklPlacement->dudika_id);
                                $set('teacher_id', $record->pklPlacement->teacher_id);
                            }
                        })
                        ->helperText('Hanya menampilkan DUDIKA yang berisi siswa dari Guru Pembimbing di atas.'),

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
                        ->imageEditor()
                        ->disk('public')
                        ->directory('monitorings')
                        ->imageResizeMode('cover')
                        ->imageResizeTargetWidth('1024')
                        ->imageResizeTargetHeight('1024')
                        ->required()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
