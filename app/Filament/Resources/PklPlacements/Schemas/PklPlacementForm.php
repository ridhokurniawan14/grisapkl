<?php

namespace App\Filament\Resources\PklPlacements\Schemas;

use App\Models\AcademicYear;
use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PklPlacementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Utama Penempatan')
                    ->description('Pilih Siswa, DUDIKA, dan Guru Pembimbing untuk penempatan ini.')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('academic_year_id')
                                ->relationship('academicYear', 'name')
                                ->label('Tahun Ajaran')
                                ->default(fn() => AcademicYear::where('is_active', true)->value('id'))
                                ->searchable()
                                ->preload()
                                ->required(),

                            // INPUT UNTUK CREATE (Bisa Pilih Banyak)
                            Select::make('student_ids')
                                ->label('Siswa PKL (Bisa pilih lebih dari 1)')
                                ->options(Student::all()->mapWithKeys(fn($s) => [$s->id => "{$s->nis} - {$s->name}"]))
                                ->multiple() // Fitur sakti pilih banyak
                                ->searchable()
                                ->required()
                                ->hiddenOn('edit'), // Hanya muncul saat form Buat Baru

                            // INPUT UNTUK EDIT (Hanya 1 Siswa)
                            Select::make('student_id')
                                ->relationship('student', 'name')
                                ->getOptionLabelFromRecordUsing(fn($record) => "{$record->nis} - {$record->name}")
                                ->label('Siswa PKL')
                                ->searchable()
                                ->required()
                                ->hiddenOn('create'), // Hanya muncul saat form Edit

                            Select::make('dudika_id')
                                ->relationship('dudika', 'name')
                                ->label('Tempat PKL (DUDIKA)')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('teacher_id')
                                ->relationship('teacher', 'name')
                                ->label('Guru Pembimbing')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),
                    ]),

                Section::make('Waktu Pelaksanaan & Bidang')
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('start_date')->label('Tanggal Mulai PKL')->required(),
                            DatePicker::make('end_date')->label('Tanggal Selesai PKL')->required(),
                            TextInput::make('pkl_field')->label('Bidang Pekerjaan (Opsional)')->placeholder('Contoh: Teknisi Jaringan'),
                            Select::make('status')
                                ->label('Status Penempatan')
                                ->options(['Aktif' => 'Aktif', 'Ditarik' => 'Ditarik'])
                                ->default('Aktif')
                                ->required(),
                        ]),
                    ]),

                Section::make('Lokasi Absensi Siswa')
                    ->description('Tentukan titik koordinat (Latitude & Longitude) tempat siswa akan melakukan absensi harian.')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('latitude')
                                ->label('Latitude (Garis Lintang)')
                                ->placeholder('Contoh: -8.219233')
                                ->numeric()
                                ->helperText('Cara ambil di Google Maps (HP/PC): Tahan layar/klik kanan di lokasi, lalu copy angka bagian DEPAN.'),
                            TextInput::make('longitude')
                                ->label('Longitude (Garis Bujur)')
                                ->placeholder('Contoh: 114.369227')
                                ->numeric()
                                ->helperText('Copy angka bagian BELAKANG dari Google Maps.'),
                            TextInput::make('radius')
                                ->label('Batas Radius (Meter)')
                                ->default(50)
                                ->numeric()
                                ->required()
                                ->helperText('Jarak maksimal siswa bisa absen dari titik (Default: 50m).'),
                        ]),
                    ])->collapsed(),
            ]);
    }
}
