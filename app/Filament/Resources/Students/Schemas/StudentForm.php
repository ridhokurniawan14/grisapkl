<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECTION 1: BIODATA DIRI
                Section::make('Biodata Diri')
                    ->description('Masukkan informasi pribadi dan data akademik siswa.')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Lengkap Siswa')
                                ->placeholder('Contoh: Miftahul Jannah')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('nis')
                                ->label('NIS (Nomor Induk Siswa)')
                                ->placeholder('Contoh: 12639/205.4.2.1')
                                ->required(), // <-- numeric() SUDAH DIHAPUS BRO!
                            TextInput::make('nisn')
                                ->label('NISN')
                                ->placeholder('Contoh: 0078182981')
                                ->numeric(),
                            TextInput::make('phone')
                                ->label('No. HP / WA Siswa')
                                ->placeholder('Contoh: 081234567890')
                                ->tel(),
                            Select::make('gender')
                                ->label('Jenis Kelamin')
                                ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                                ->required(),
                            Select::make('student_class_id')
                                ->relationship('studentClass', 'name')
                                ->label('Kelas')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('academic_year_id')
                                ->relationship('academicYear', 'name')
                                ->label('Tahun Ajaran')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('birth_place')
                                ->label('Tempat Lahir')
                                ->placeholder('Contoh: Banyuwangi'),
                            DatePicker::make('birth_date')
                                ->label('Tanggal Lahir')
                                ->helperText('Digunakan sebagai Password Default (format: DDMMYYYY).'),
                            TextInput::make('religion')
                                ->label('Agama')
                                ->placeholder('Contoh: Islam'),
                        ]),
                        Textarea::make('address')
                            ->label('Alamat Lengkap Siswa')
                            ->placeholder('Masukkan alamat domisili saat ini...')
                            ->columnSpanFull(),
                    ]),

                // SECTION 2: DATA ORANG TUA
                Section::make('Data Orang Tua / Wali')
                    ->description('Informasi kontak darurat dan data orang tua siswa.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('father_name')->label('Nama Ayah')->placeholder('Nama lengkap ayah...'),
                            TextInput::make('father_job')->label('Pekerjaan Ayah')->placeholder('Contoh: Wiraswasta'),
                            TextInput::make('mother_name')->label('Nama Ibu')->placeholder('Nama lengkap ibu...'),
                            TextInput::make('mother_job')->label('Pekerjaan Ibu')->placeholder('Contoh: Ibu Rumah Tangga'),
                            TextInput::make('parent_phone')->label('No. HP / WA Orang Tua')->tel()->placeholder('Contoh: 081234567890'),
                        ]),

                        // FITUR CENTANG SAKTI (AUTO-FILL ALAMAT)
                        Checkbox::make('is_same_address')
                            ->label('Centang jika Alamat Orang Tua SAMA dengan Alamat Siswa')
                            ->live() // Aktifkan real-time
                            ->dehydrated(false) // Jangan simpan kolom checkbox ini ke database
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                // Jika dicentang, copy isi 'address' ke 'parent_address'
                                if ($state) {
                                    $set('parent_address', $get('address'));
                                } else {
                                    // Jika centang dilepas, kosongkan lagi
                                    $set('parent_address', null);
                                }
                            }),

                        Textarea::make('parent_address')
                            ->label('Alamat Orang Tua')
                            ->placeholder('Isi jika berbeda dengan alamat siswa...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
