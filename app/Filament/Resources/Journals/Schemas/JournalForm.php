<?php

namespace App\Filament\Resources\Journals\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class JournalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    Select::make('pkl_placement_id')
                        ->label('Siswa & Tempat PKL')
                        ->placeholder('Cari nama siswa atau nama DUDIKA...')
                        ->options(function () {
                            return \App\Models\PklPlacement::with(['student', 'dudika'])->get()->mapWithKeys(function ($placement) {
                                return [$placement->id => $placement->student->name . ' - ' . $placement->dudika->name];
                            });
                        })
                        ->searchable()
                        ->required()
                        ->live() // <--- MANTRA SAKTI 1: Biar form di bawahnya bisa bereaksi
                        ->helperText('Pilih siswa yang melakukan kegiatan ini.'),

                    Select::make('attend_status')
                        ->label('Status Kehadiran')
                        ->options([
                            'Hadir' => 'Hadir',
                            'Sakit' => 'Sakit',
                            'Izin' => 'Izin'
                        ])
                        ->default('Hadir')
                        ->required()
                        ->helperText('Pilih status kehadiran siswa hari ini.'),
                ]),

                Grid::make(2)->schema([
                    DatePicker::make('date')
                        ->label('Tanggal Kegiatan')
                        ->default(now())
                        ->required()
                        ->helperText('Tanggal pelaksanaan jurnal (Otomatis hari ini).'),

                    TimePicker::make('time')
                        ->label('Waktu / Jam')
                        ->default(now())
                        ->required()
                        ->helperText('Waktu saat absen atau kegiatan dilakukan.'),
                ]),

                Textarea::make('activity')
                    ->label('Detail Kegiatan yang Dilakukan')
                    ->placeholder('Contoh: Melakukan instalasi Windows 11 pada 5 PC Lab komputer...')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull()
                    ->helperText('Jelaskan kegiatan secara rinci minimal 1 kalimat.'),

                FileUpload::make('photo_path')
                    ->label('Foto Bukti Kegiatan / Selfie')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->replacesExistingFiles()
                    ->directory('journals')
                    ->imagePreviewHeight('250')
                    ->imageResizeMode('cover')
                    ->imageResizeTargetWidth('1024')
                    ->imageResizeTargetHeight('1024')
                    ->columnSpanFull()
                    ->helperText('Sistem akan mengompres foto secara otomatis. Pastikan foto tidak blur.'),

                // MANTRA SAKTI 2: LOKASI DENGAN ERROR HANDLING
                Grid::make(2)
                    ->visible(fn(Get $get) => filled($get('pkl_placement_id')))
                    ->schema([
                        TextInput::make('latitude')
                            ->label('Latitude Lokasi')
                            ->readOnly()
                            ->placeholder('Mendeteksi koordinat GPS...')
                            ->extraAlpineAttributes([
                                'x-init' => "
                    if (!window.isSecureContext) {
                        \$el.placeholder = 'Butuh HTTPS atau akses via localhost!';
                        return;
                    }
                    if (!navigator.geolocation) {
                        \$el.placeholder = 'GPS tidak didukung browser ini.';
                        return;
                    }
                    \$el.placeholder = 'Sedang mengambil koordinat...';
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            \$wire.\$set('data.latitude', pos.coords.latitude.toFixed(7));
                            \$wire.\$set('data.longitude', pos.coords.longitude.toFixed(7));
                        },
                        (err) => {
                            const msg = ['', 'Izin lokasi ditolak browser.', 'Sinyal GPS tidak ada.', 'Timeout, refresh halaman.'];
                            \$el.placeholder = msg[err.code] || 'Gagal mendapat lokasi.';
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                "
                            ])
                            ->helperText('Otomatis terisi. Pastikan izin lokasi diaktifkan di browser.'),

                        TextInput::make('longitude')
                            ->label('Longitude Lokasi')
                            ->readOnly()
                            ->placeholder('Menunggu latitude...')
                            ->helperText('Terisi otomatis bersamaan dengan latitude.'),
                    ]),
                Grid::make(2)->schema([
                    Toggle::make('is_valid')
                        ->label('Status Validasi DUDIKA')
                        ->helperText('Matikan jika DUDIKA meminta siswa melakukan revisi/perbaikan.')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger'),

                    TextInput::make('revision_note')
                        ->label('Catatan Revisi dari DUDIKA (Opsional)')
                        ->placeholder('Contoh: Foto kegiatan kurang jelas, tolong foto ulang.')
                        ->helperText('Wajib diisi jika Status Validasi dimatikan.'),
                ]),
            ]);
    }
}
