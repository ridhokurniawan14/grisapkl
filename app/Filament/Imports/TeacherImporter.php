<?php

namespace App\Filament\Imports;

use App\Models\Teacher;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TeacherImporter extends Importer
{
    protected static ?string $model = Teacher::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nama Guru')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('title') // Tambahkan kolom gelar
                ->label('Gelar')
                ->rules(['max:255']),

            ImportColumn::make('nip')
                ->label('NIP')
                ->rules(['max:255']),

            ImportColumn::make('phone')
                ->label('No. HP')
                ->fillRecordUsing(function ($record, $state) {
                    if (blank($state)) return;
                    $phone = preg_replace('/[^0-9+]/', '', $state);
                    if (str_starts_with($phone, '0')) {
                        $phone = '+62' . substr($phone, 1);
                    }
                    $record->phone = $phone;
                }),

            ImportColumn::make('subject')
                ->label('Mata Pelajaran'),
        ];
    }

    public function resolveRecord(): ?Teacher
    {
        return Teacher::firstOrNew([
            'name' => $this->data['name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import Data Guru selesai. ' . number_format($import->successful_rows) . ' baris berhasil masuk.';
        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' Ada ' . number_format($failedRowsCount) . ' baris yang gagal.';
        }
        return $body;
    }
}
