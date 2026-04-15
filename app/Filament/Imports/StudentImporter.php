<?php

namespace App\Filament\Imports;

use App\Models\Student;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('user_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('student_class_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
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
            ImportColumn::make('father_name')
                ->rules(['max:255']),
            ImportColumn::make('mother_name')
                ->rules(['max:255']),
            ImportColumn::make('father_job')
                ->rules(['max:255']),
            ImportColumn::make('mother_job')
                ->rules(['max:255']),
            ImportColumn::make('parent_address'),
            ImportColumn::make('parent_phone')
                ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): Student
    {
        return Student::firstOrNew([
            'nis' => $this->data['nis'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your student import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
