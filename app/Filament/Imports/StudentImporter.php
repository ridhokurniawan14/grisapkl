<?php

namespace App\Filament\Imports;

use App\Models\Student;
use App\Models\StudentClass;
use App\Models\AcademicYear;
use App\Models\User;
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
            ImportColumn::make('name')
                ->label('Nama Lengkap Siswa')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('nis')
                ->label('NIS')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            // TAMBAHAN: NISN & PHONE
            ImportColumn::make('nisn')
                ->label('NISN')
                ->rules(['max:255']),

            ImportColumn::make('phone')
                ->label('No. HP Siswa')
                ->fillRecordUsing(function ($record, $state) {
                    if (blank($state)) return;
                    $phone = preg_replace('/[^0-9+]/', '', $state);
                    if (str_starts_with($phone, '0')) {
                        $phone = '+62' . substr($phone, 1);
                    }
                    $record->phone = $phone;
                }),

            ImportColumn::make('gender')
                ->label('L/P')
                ->requiredMapping()
                ->rules(['required']),

            ImportColumn::make('birth_place')->label('Tempat Lahir')->rules(['max:255']),
            ImportColumn::make('birth_date')->label('Tgl Lahir')->rules(['date']),
            ImportColumn::make('religion')->label('Agama')->rules(['max:255']),
            ImportColumn::make('address')->label('Alamat Lengkap'),
            ImportColumn::make('parent_phone')->label('No. HP Ortu')->rules(['max:255']),
        ];
    }

    public function resolveRecord(): Student
    {
        return Student::firstOrNew([
            'nis' => $this->data['nis'],
        ]);
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Select::make('student_class_id')
                ->label('Pilih Kelas untuk Data Ini')
                ->options(StudentClass::pluck('name', 'id'))
                ->searchable()
                ->required(),
            Select::make('academic_year_id')
                ->label('Pilih Tahun Ajaran')
                ->options(AcademicYear::where('is_active', true)->pluck('name', 'id'))
                ->searchable()
                ->required(),
        ];
    }

    public function beforeCreate(): void
    {
        app()->instance('importing.student', true);
    }
    public function beforeSave(): void
    {
        app()->instance('importing.student', true);
        $this->record->student_class_id = $this->options['student_class_id'];
        $this->record->academic_year_id = $this->options['academic_year_id'];
    }
    public function afterCreate(): void
    {
        $this->createUserForRecord();
    }
    public function afterSave(): void
    {
        $this->createUserForRecord();
        app()->forgetInstance('importing.student');
    }

    private function createUserForRecord(): void
    {
        $record = $this->record->fresh();
        if ($record->user_id || blank($record->name)) return;

        // Password = NIS, Username = NIS@smk...
        $password = $record->nis;
        $email = $record->nis . '@smkpgri1giri.sch.id';

        $user = User::firstOrNew(['email' => $email]);
        $user->name = $record->name;

        if (!$user->exists) $user->password = bcrypt($password);
        $user->save();

        if (!$user->hasRole('Siswa')) $user->assignRole('Siswa');
        $record->updateQuietly(['user_id' => $user->id]);
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
