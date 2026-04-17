<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECTION 1: BIODATA DIRI (Otomatis Terbuka)
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
                                ->placeholder('Contoh: 12301')
                                ->required()
                                ->numeric(),
                            TextInput::make('nisn')
                                ->label('NISN')
                                ->placeholder('Contoh: 0078182981')
                                ->numeric(),
                            TextInput::make('phone') // <-- KOLOM BARU KITA
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

                // SECTION 2: DATA ORANG TUA (Otomatis Tertutup/Collapse)
                Section::make('Data Orang Tua / Wali')
                    ->description('Informasi kontak darurat dan data orang tua siswa.')
                    ->collapsible()
                    ->collapsed() // <-- Bikin otomatis tertutup
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('father_name')->label('Nama Ayah')->placeholder('Nama lengkap ayah...'),
                            TextInput::make('father_job')->label('Pekerjaan Ayah')->placeholder('Contoh: Wiraswasta'),
                            TextInput::make('mother_name')->label('Nama Ibu')->placeholder('Nama lengkap ibu...'),
                            TextInput::make('mother_job')->label('Pekerjaan Ibu')->placeholder('Contoh: Ibu Rumah Tangga'),
                            TextInput::make('parent_phone')->label('No. HP / WA Orang Tua')->tel()->placeholder('Contoh: 081234567890'),
                        ]),
                        Textarea::make('parent_address')
                            ->label('Alamat Orang Tua')
                            ->placeholder('Isi jika berbeda dengan alamat siswa...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
