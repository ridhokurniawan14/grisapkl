<?php

namespace App\Filament\Resources\PklPlacements\Schemas;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\Student;
use Dotswan\MapPicker\Fields\Map; // <-- Import Plugin Peta
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\VerticalAlignment;

class PklPlacementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs Penempatan')
                    ->tabs([
                        // TAB 1: DATA UTAMA
                        Tab::make('Data Utama')
                            ->icon('heroicon-m-user-group')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('academic_year_id')
                                        ->relationship('academicYear', 'name')
                                        ->label('Tahun Ajaran')
                                        ->default(fn() => AcademicYear::where('is_active', true)->value('id'))
                                        ->searchable()
                                        ->preload()
                                        ->required(),

                                    Select::make('major_id')
                                        ->label('Filter Jurusan (Opsional)')
                                        ->options(Major::pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->dehydrated(false) // <-- OBAT ERROR SQL (Jangan disimpan ke DB)
                                        ->placeholder('Pilih jurusan untuk menyaring daftar siswa'),
                                ]),

                                Select::make('student_ids')
                                    ->label('Siswa PKL (Bisa pilih lebih dari 1)')
                                    ->options(function (Get $get, ?Model $record) {
                                        $majorId = $get('major_id');
                                        $query = Student::query();

                                        if ($majorId) {
                                            $query->whereHas('studentClass', function ($q) use ($majorId) {
                                                $q->where('major_id', $majorId);
                                            });
                                        }

                                        // LOGIKA SAKTI: Ambil semua ID siswa yang sudah PKL
                                        $placedStudentIds = \App\Models\PklPlacement::pluck('student_id')->toArray();

                                        // Keluarkan dari daftar pencarian agar tidak bisa dipilih lagi
                                        $query->whereNotIn('id', $placedStudentIds);

                                        return $query->get()->mapWithKeys(fn($s) => [$s->id => "{$s->nis} - {$s->name}"]);
                                    })
                                    ->multiple()
                                    ->searchable()
                                    ->required()
                                    ->hiddenOn('edit')
                                    ->columnSpanFull(),

                                Select::make('student_id')
                                    ->label('Siswa PKL')
                                    ->options(function (Get $get, ?Model $record) {
                                        $majorId = $get('major_id');
                                        $query = Student::query();

                                        if ($majorId) {
                                            $query->whereHas('studentClass', function ($q) use ($majorId) {
                                                $q->where('major_id', $majorId);
                                            });
                                        }

                                        $placedStudentIds = \App\Models\PklPlacement::pluck('student_id')->toArray();

                                        // PENGECUALIAN SAAT EDIT: Siswa yang sedang diedit harus tetap muncul
                                        if ($record && $record->student_id) {
                                            $placedStudentIds = array_diff($placedStudentIds, [$record->student_id]);
                                        }

                                        $query->whereNotIn('id', $placedStudentIds);

                                        return $query->get()->mapWithKeys(fn($s) => [$s->id => "{$s->nis} - {$s->name}"]);
                                    })
                                    ->searchable()
                                    ->required()
                                    ->hiddenOn('create')
                                    ->columnSpanFull(),
                                Grid::make(2)->schema([
                                    Select::make('dudika_id')
                                        ->relationship('dudika', 'name')
                                        ->label('Tempat PKL (DUDIKA)')
                                        ->searchable()
                                        ->preload()
                                        ->live() // 1. Aktifkan mode Real-Time
                                        ->afterStateUpdated(function (Set $set, $state) {
                                            // 2. Saat DUDIKA dipilih, ambil data lokasinya
                                            if ($state) {
                                                $dudika = \App\Models\Dudika::find($state);

                                                // 3. Jika DUDIKA tersebut punya koordinat, tembakkan ke form lokasi
                                                if ($dudika && $dudika->latitude && $dudika->longitude) {
                                                    $set('latitude', $dudika->latitude);
                                                    $set('longitude', $dudika->longitude);
                                                    $set('radius', $dudika->radius ?? 50);

                                                    // Geser pin petanya juga secara otomatis!
                                                    $set('location', [
                                                        'lat' => $dudika->latitude,
                                                        'lng' => $dudika->longitude
                                                    ]);
                                                }
                                            }
                                        })
                                        ->required(),
                                    Select::make('teacher_id')
                                        ->relationship('teacher', 'name')
                                        ->label('Guru Pembimbing')
                                        ->searchable()
                                        ->preload()
                                        ->required(),
                                ]),
                            ]),

                        // TAB 2: WAKTU & BIDANG
                        Tab::make('Waktu & Bidang')
                            ->icon('heroicon-m-calendar-days')
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

                        // TAB 3: LOKASI ABSENSI
                        Tab::make('Lokasi Absensi')
                            ->icon('heroicon-m-map-pin')
                            ->schema([

                                // === BLOK PENCARIAN ALAMAT (GANTI showSearchBar) ===
                                Grid::make(5)->schema([
                                    TextInput::make('address_search')
                                        ->label('Cari Alamat / Tempat')
                                        ->placeholder('Ketik nama tempat, lalu klik "Cari"...')
                                        ->dehydrated(false) // Tidak disimpan ke DB
                                        ->columnSpan(4),

                                    Actions::make([
                                        Action::make('search_address')
                                            ->label('Cari')
                                            ->icon('heroicon-m-magnifying-glass')
                                            ->action(function (Get $get, Set $set, $livewire): void {
                                                $query = $get('address_search');
                                                if (!$query) return;

                                                // Hit Nominatim API (OpenStreetMap, gratis)
                                                $response = \Illuminate\Support\Facades\Http::withHeaders([
                                                    'User-Agent' => config('app.name') . ' location-search',
                                                ])->get('https://nominatim.openstreetmap.org/search', [
                                                    'q'      => $query,
                                                    'format' => 'json',
                                                    'limit'  => 1,
                                                ]);

                                                $results = $response->json();

                                                if (!empty($results)) {
                                                    $lat = (float) $results[0]['lat'];
                                                    $lng = (float) $results[0]['lon'];

                                                    $set('latitude', $lat);
                                                    $set('longitude', $lng);
                                                    $set('location', ['lat' => $lat, 'lng' => $lng]);

                                                    // Refresh peta agar pin pindah ke lokasi baru
                                                    $livewire->dispatch('refreshMap');
                                                } else {
                                                    \Filament\Notifications\Notification::make()
                                                        ->title('Lokasi tidak ditemukan')
                                                        ->body('Coba kata kunci yang lebih spesifik.')
                                                        ->warning()
                                                        ->send();
                                                }
                                            }),
                                    ])->verticalAlignment(VerticalAlignment::End)
                                        ->columnSpan(1),
                                ]),
                                // ===================================================
                                Map::make('location')
                                    ->label('Peta Interaktif (Cari Tempat & Geser Pin)')
                                    ->columnSpanFull()
                                    ->defaultLocation(latitude: -8.219233, longitude: 114.369227)
                                    ->dehydrated(false)
                                    // TRIK SAKTI: Membajak Satelit Google Maps (Hybrid) 
                                    ->tilesUrl('http://mt0.google.com/vt/lyrs=y&hl=en&x={x}&y={y}&z={z}')
                                    ->afterStateUpdated(function (Set $set, ?array $state): void {
                                        $set('latitude', $state['lat'] ?? null);
                                        $set('longitude', $state['lng'] ?? null);
                                    })
                                    ->afterStateHydrated(function ($state, $record, Set $set): void {
                                        if ($record && $record->latitude && $record->longitude) {
                                            $set('location', ['lat' => $record->latitude, 'lng' => $record->longitude]);
                                        }
                                    })
                                    ->extraStyles([
                                        'min-height: 400px',
                                        'border-radius: 12px'
                                    ])
                                    ->liveLocation(true, true, 5000)
                                    ->showMarker(true)
                                    ->markerColor("#ef4444")
                                    ->showFullscreenControl(true)
                                    ->showZoomControl(true)
                                    ->draggable(true)
                                    ->clickable(true),

                                // === BLOK KOORDINAT ===
                                Grid::make(3)->schema([
                                    TextInput::make('latitude')
                                        ->label('Latitude (Bisa Diisi Manual)')
                                        ->numeric()
                                        ->required()
                                        ->live(onBlur: true) // Aktif saat user selesai ngetik
                                        ->afterStateUpdated(function (Set $set, \Filament\Forms\Get $get, $state, $livewire) {
                                            $set('location', ['lat' => (float)$state, 'lng' => (float)$get('longitude')]);
                                            $livewire->dispatch('refreshMap'); // Peta otomatis geser
                                        }),

                                    TextInput::make('longitude')
                                        ->label('Longitude (Bisa Diisi Manual)')
                                        ->numeric()
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (Set $set, Get $get, $state, $livewire) {
                                            $set('location', ['lat' => (float)$get('latitude'), 'lng' => (float)$state]);
                                            $livewire->dispatch('refreshMap'); // Peta otomatis geser
                                        }),

                                    TextInput::make('radius')
                                        ->label('Batas Radius (Meter)')
                                        ->default(50)
                                        ->numeric()
                                        ->required()
                                        ->helperText('Jarak maksimal absen (Default: 50m).'),
                                ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
