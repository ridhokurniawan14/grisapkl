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

                    $phone = preg_replace('/[^0-9+]/', '', $state);
                    if (str_starts_with($phone, '0')) {
                        $phone = '+62' . substr($phone, 1);
                    }
                    $record->supervisor_phone = $phone;
                }),
        ];
    }

    public function resolveRecord(): ?Dudika
    {
        return Dudika::firstOrNew([
            'name'    => $this->data['name'] ?? null,
            'address' => $this->data['address'] ?? null,
        ]);
    }

    /**
     * Jalankan setelah Dudika berhasil dibuat (hanya untuk record baru)
     */
    public function afterCreate(): void
    {
        $record = $this->record;

        // Skip kalau sudah punya user atau data penting kosong
        if ($record->user_id || blank($record->supervisor_name) || blank($record->supervisor_phone)) {
            return;
        }

        // Bersihkan nomor HP, ambil hanya 5 digit terakhir
        $phone = preg_replace('/[^0-9]/', '', $record->supervisor_phone);
        $lastFive = substr($phone, -5);                    // ← ini yang penting

        $email = $lastFive . '@smkpgri1giri.sch.id';

        // Buat atau ambil user berdasarkan email
        $user = User::firstOrNew(['email' => $email]);

        $user->name = $record->supervisor_name;

        if (!$user->exists) {
            $user->password = bcrypt($lastFive);   // password juga 5 digit terakhir
        }

        $user->save();

        // Assign role (pastikan nama role sama persis dengan di database)
        if (!$user->hasRole('Dudika')) {
            $user->assignRole('Dudika');
        }

        // Update user_id tanpa memicu event saved()
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
