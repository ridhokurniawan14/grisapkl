<?php

namespace App\Filament\Imports;

use App\Models\Dudika;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class DudikaImporter extends Importer
{
    protected static ?string $model = Dudika::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('address')
                ->label('Alamat')
                ->rules(['max:255']),
            ImportColumn::make('head_name')
                ->label('Nama Pimpinan')
                ->rules(['max:255']),
            ImportColumn::make('head_nip')
                ->label('NIP Pimpinan')
                ->rules(['max:255']),
            ImportColumn::make('supervisor_name')
                ->label('Nama Pembimbing')
                ->rules(['max:255']),
            ImportColumn::make('supervisor_nip')
                ->label('NIP Pembimbing')
                ->rules(['max:255']),
            ImportColumn::make('supervisor_phone')
                ->label('No. HP Pembimbing')
                ->rules(['max:20'])
                ->fillRecordUsing(function ($record, $state) {
                    if (blank($state)) return;

                    // Simpan sebagai digit murni saja — lebih konsisten
                    $record->supervisor_phone = preg_replace('/[^0-9]/', '', $state);
                }),
        ];
    }

    public function resolveRecord(): ?Dudika
    {
        return Dudika::firstOrNew([
            'name' => $this->data['name'] ?? null,
        ]);
    }

    /**
     * Tandai bahwa sedang dalam proses import
     * supaya observer tidak ikut membuat user (hindari double execution)
     */
    public function beforeCreate(): void
    {
        app()->instance('importing.dudika', true);
    }

    public function beforeSave(): void
    {
        app()->instance('importing.dudika', true);
    }

    /**
     * Satu-satunya tempat pembuatan user saat import
     */
    public function afterCreate(): void
    {
        $this->createUserForRecord();
    }

    public function afterSave(): void
    {
        // Tangani juga saat update via import (resolveRecord return existing)
        $this->createUserForRecord();

        // Lepas flag setelah selesai
        app()->forgetInstance('importing.dudika');
    }

    private function createUserForRecord(): void
    {
        // Ambil fresh dari DB supaya user_id yang diset updateQuietly terbaca
        $record = $this->record->fresh();

        if ($record->user_id || blank($record->supervisor_name) || blank($record->supervisor_phone)) {
            return;
        }

        // Phone sudah digit murni dari fillRecordUsing
        $phone    = preg_replace('/[^0-9]/', '', $record->supervisor_phone);
        $lastFive = substr($phone, -5);
        $email    = $lastFive . '@smkpgri1giri.sch.id';

        $user       = User::firstOrNew(['email' => $email]);
        $user->name = $record->supervisor_name;

        if (!$user->exists) {
            $user->password = bcrypt($lastFive);
        }

        $user->save();

        if (!$user->hasRole('Dudika')) {
            $user->assignRole('Dudika');
        }

        $record->updateQuietly(['user_id' => $user->id]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import DUDIKA selesai. ' . number_format($import->successful_rows) . ' baris berhasil masuk.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' Ada ' . number_format($failedRowsCount) . ' baris yang gagal.';
        }

        return $body;
    }
}
