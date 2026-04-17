<?php

namespace App\Filament\Imports;

use App\Models\Teacher;
use App\Models\User;
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

            ImportColumn::make('title')
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
            'name' => $this->data['name'] ?? null,
        ]);
    }

    // TANDAI BAHWA SEDANG IMPORT (Agar Observer Diam)
    public function beforeCreate(): void
    {
        app()->instance('importing.teacher', true);
    }

    public function beforeSave(): void
    {
        app()->instance('importing.teacher', true);
    }

    public function afterCreate(): void
    {
        $this->createUserForRecord();
    }

    public function afterSave(): void
    {
        $this->createUserForRecord();
        app()->forgetInstance('importing.teacher');
    }

    // LOGIC PEMBUATAN AKUN SAAT IMPORT
    private function createUserForRecord(): void
    {
        $record = $this->record->fresh();

        if ($record->user_id || blank($record->name)) {
            return;
        }

        $fullNameWithTitle = $record->title
            ? $record->name . ', ' . $record->title
            : $record->name;

        // 1. Ambil kata depan saja untuk Email
        $firstName = strtolower(explode(' ', trim($record->name))[0]);
        $baseUsername = preg_replace('/[^a-z0-9]/', '', $firstName);
        if (empty($baseUsername)) $baseUsername = 'guru';

        $username = $baseUsername;
        $email = $username . '@smkpgri1giri.sch.id';
        $counter = 1;

        while (User::where('email', $email)->exists()) {
            $username = $baseUsername . $counter;
            $email = $username . '@smkpgri1giri.sch.id';
            $counter++;
        }

        // 2. Password 5 digit HP
        $phone = preg_replace('/[^0-9]/', '', $record->phone ?? '12345');
        $password = substr($phone, -5);
        if (strlen($password) < 5) {
            $password = '12345';
        }

        // 3. Eksekusi Akun
        $user = User::firstOrNew(['email' => $email]);
        $user->name = $fullNameWithTitle;

        if (!$user->exists) {
            $user->password = bcrypt($password);
        }

        $user->save();

        if (!$user->hasRole('Guru')) {
            $user->assignRole('Guru');
        }

        $record->updateQuietly(['user_id' => $user->id]);
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
