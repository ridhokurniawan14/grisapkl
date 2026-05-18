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
                ->label('Nama DUDIKA')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('email')
                ->label('Email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255'])
                ->fillRecordUsing(function ($record, $state) {}),

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

    public function beforeCreate(): void
    {
        app()->instance('importing.dudika', true);
    }
    public function beforeSave(): void
    {
        app()->instance('importing.dudika', true);
    }
    public function afterCreate(): void
    {
        $this->createUserForRecord();
    }
    public function afterSave(): void
    {
        $this->createUserForRecord();
        app()->forgetInstance('importing.dudika');
    }

    private function createUserForRecord(): void
    {
        $record = $this->record->fresh();

        // Ambil data email langsung dari baris Excel yang sedang diproses!
        $email = $this->data['email'] ?? null;

        if ($record->user_id || blank($email)) {
            return;
        }

        $user = User::firstOrNew(['email' => $email]);

        if (!$user->exists) {
            $user->name = $record->supervisor_name ?? $record->name;
            $user->password = bcrypt('12345'); // Default password 12345
            $user->save();

            $roleName = \Spatie\Permission\Models\Role::where('name', 'dudika')->exists() ? 'dudika' : 'Dudika';
            $user->assignRole($roleName);
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
