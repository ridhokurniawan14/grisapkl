<?php

namespace App\Filament\Resources\SchoolProfiles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SchoolProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Profil Sekolah')
                    ->tabs([
                        // TAB 1: IDENTITAS
                        Tab::make('Identitas Sekolah')
                            ->icon('heroicon-m-building-office-2')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('name')
                                        ->label('Nama Sekolah')
                                        ->required()
                                        ->placeholder('Contoh: SMK PGRI 1 GIRI')
                                        ->default('SMK PGRI 1 GIRI'),

                                    TextInput::make('npsn')
                                        ->label('NPSN')
                                        ->numeric()
                                        ->placeholder('Contoh: 20500123')
                                        ->helperText('Nomor Pokok Sekolah Nasional (8 digit).'),

                                    TextInput::make('email')
                                        ->label('Email Resmi')
                                        ->email()
                                        ->placeholder('Contoh: info@smkpgri1giri.sch.id'),

                                    TextInput::make('phone')
                                        ->label('Nomor Telepon')
                                        ->tel()
                                        ->placeholder('Contoh: (0333) 412345'),

                                    TextInput::make('website')
                                        ->label('Website Sekolah')
                                        ->url()
                                        ->placeholder('Contoh: https://smkpgri1giri.sch.id')
                                        ->columnSpanFull(),
                                ]),
                            ]),

                        // TAB 2: ALAMAT
                        Tab::make('Alamat Lengkap')
                            ->icon('heroicon-m-map-pin')
                            ->schema([
                                Textarea::make('address')
                                    ->label('Alamat Jalan')
                                    ->placeholder('Contoh: Jl. Gajah Mada No. 14, Lingkungan Cungking, Kel. Mojopanggung')
                                    ->columnSpanFull(),

                                Grid::make(2)->schema([
                                    TextInput::make('city')
                                        ->label('Kota / Kabupaten')
                                        ->placeholder('Contoh: Banyuwangi'),

                                    TextInput::make('postal_code')
                                        ->label('Kode Pos')
                                        ->numeric()
                                        ->placeholder('Contoh: 68422'),
                                ]),
                            ]),

                        // TAB 3: KEPSEK, LOGO & KOP SURAT
                        Tab::make('Pengesahan & Surat')
                            ->icon('heroicon-m-check-badge')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('headmaster_name')
                                        ->label('Nama Kepala Sekolah')
                                        ->placeholder('Contoh: Drs. H. Budi Santoso, M.Pd.'),

                                    TextInput::make('headmaster_nip')
                                        ->label('NIP Kepala Sekolah')
                                        ->placeholder('Contoh: 19700101 199512 1 001')
                                        ->helperText('Kosongkan jika bukan berstatus PNS.'),

                                    TextInput::make('surat_pengantar_nomor')
                                        ->label('Format Nomor Surat Pengantar')
                                        ->placeholder('Contoh: 1437/M.3/SMK PGRI 1 GIRI/26/IX/2025')
                                        ->columnSpanFull()
                                        ->helperText('Nomor ini akan dipakai di semua cetakan Surat Pengantar PKL.'),

                                    FileUpload::make('logo_path')
                                        ->label('Logo Sekolah')
                                        ->image()
                                        ->disk('public')
                                        ->imageEditor()
                                        ->imagePreviewHeight('250')
                                        ->directory('logos')
                                        ->helperText('Format: JPG/PNG. Rekomendasi rasio kotak 1:1.'),

                                    FileUpload::make('signature_path')
                                        ->label('Tanda Tangan Kepala Sekolah')
                                        ->image()
                                        ->imageEditor()
                                        ->disk('public')
                                        ->imagePreviewHeight('250')
                                        ->directory('signatures')
                                        ->helperText('Gunakan gambar berlatar transparan (.png).'),

                                    FileUpload::make('kop_surat_path')
                                        ->label('Gambar KOP Surat Full')
                                        ->image()
                                        ->imageEditor()
                                        ->disk('public')
                                        ->imagePreviewHeight('250')
                                        ->directory('kopsurat')
                                        ->helperText('Upload gambar KOP Surat memanjang (Rekomendasi rasio banner/header).'),

                                    FileUpload::make('cover_laporan_path')
                                        ->label('Gambar Cover Laporan PKL')
                                        ->image()
                                        ->imageEditor()
                                        ->disk('public')
                                        ->imagePreviewHeight('250')
                                        ->directory('covers')
                                        ->helperText('Upload gambar Cover Laporan PKL. Rekomendasi portrait A4 (PNG).'),
                                ]),
                            ]),

                        // TAB 4: PENGATURAN APLIKASI
                        Tab::make('Pengaturan PKL')
                            ->icon('heroicon-m-cog-8-tooth')
                            ->schema([
                                \Filament\Forms\Components\Toggle::make('is_radius_attendance_enabled')
                                    ->label('Aktifkan Validasi Radius Absensi')
                                    ->helperText('Jika menyala, siswa WAJIB berada di dalam radius DUDIKA untuk bisa absen.')
                                    ->default(true)
                                    ->onColor('success')
                                    ->offColor('danger'),

                                // ==========================================
                                // MANTRA SAKTI: TOGGLE TTD GURU PEMBIMBING
                                // ==========================================
                                \Filament\Forms\Components\Toggle::make('is_teacher_signature_enabled')
                                    ->label('Tampilkan Tanda Tangan Guru Pembimbing')
                                    ->helperText('Matikan (OFF) jika Anda mewajibkan Guru Pembimbing menggunakan Tanda Tangan Basah (Asli) pada cetakan PDF Laporan.')
                                    ->default(true)
                                    ->onColor('success')
                                    ->offColor('danger'),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
