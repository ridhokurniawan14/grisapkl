<?php

namespace App\Filament\Imports;

use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Select;
use Illuminate\Support\Number;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            // Tambahkan kolom Nama (Tidak ada di tabel student, jadi kita cegah agar tidak error)
            ImportColumn::make('name')
                ->label('Nama Lengkap Siswa')
                ->requiredMapping()
                ->fillRecordUsing(fn($record, $state) => null), // Cegah masuk ke tabel students

            ImportColumn::make('nis')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('gender')
                ->requiredMapping()
                ->rules(['required']),

            ImportColumn::make('birth_place')
                ->rules(['max:255']),

            ImportColumn::make('birth_date')
                ->rules(['date']),

            ImportColumn::make('religion')
                ->rules(['max:255']),

            ImportColumn::make('address'),

            ImportColumn::make('father_name')->rules(['max:255']),
            ImportColumn::make('mother_name')->rules(['max:255']),
            ImportColumn::make('father_job')->rules(['max:255']),
            ImportColumn::make('mother_job')->rules(['max:255']),
            ImportColumn::make('parent_address'),
            ImportColumn::make('parent_phone')->rules(['max:255']),
        ];
    }

    public function resolveRecord(): Student
    {
        return Student::firstOrNew([
            'nis' => $this->data['nis'],
        ]);
    }

    // 1. TAMBAHKAN FUNGSI INI UNTUK MENAMPILKAN DROPDOWN KELAS
    public static function getOptionsFormComponents(): array
    {
        return [
            Select::make('student_class_id')
                ->label('Pilih Kelas untuk Data Ini')
                ->options(StudentClass::pluck('name', 'id'))
                ->searchable()
                ->required(),
        ];
    }

    // 2. FUNGSI BEFORE SAVE
    protected function beforeSave(): void
    {
        // Mengambil ID Kelas dari form options di atas
        $this->record->student_class_id = $this->options['student_class_id'];

        $password = isset($this->data['birth_date'])
            ? Carbon::parse($this->data['birth_date'])->format('dmY')
            : '12345678';

        $user = User::firstOrCreate(
            ['email' => $this->data['nis'] . '@smk.com'],
            [
                'name' => $this->data['name'],
                'password' => bcrypt($password),
            ]
        );

        if (!$user->hasRole('Siswa')) {
            $user->assignRole('Siswa');
        }

        $this->record->user_id = $user->id;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import data siswa selesai! ' . Number::format($import->successful_rows) . ' baris berhasil masuk.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' baris gagal (cek file log).';
        }

        return $body;
    }
}
