<?php

namespace App\Filament\Imports;

use App\Models\Dudika;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class DudikaImporter extends Importer
{
    protected static ?string $model = Dudika::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('address'),
            ImportColumn::make('head_name')
                ->rules(['max:255']),
            ImportColumn::make('head_nip')
                ->rules(['max:255']),
            ImportColumn::make('supervisor_name')
                ->rules(['max:255']),
            ImportColumn::make('supervisor_nip')
                ->rules(['max:255']),
            ImportColumn::make('supervisor_phone')
                ->label('No. HP Pembimbing')
                ->fillRecordUsing(function ($record, $state) {
                    // Jika di excel kosong, biarkan saja
                    if (blank($state)) return;

                    // 1. Bersihkan semua karakter KECUALI angka dan tanda plus (+)
                    // Jadi spasi dan strip (-) akan otomatis hilang
                    $phone = preg_replace('/[^0-9+]/', '', $state);

                    // 2. Jika nomor diawali dengan angka '0', ubah menjadi '+62'
                    if (str_starts_with($phone, '0')) {
                        $phone = '+62' . substr($phone, 1);
                    }

                    // 3. Masukkan ke database
                    $record->supervisor_phone = $phone;
                }),
        ];
    }

    public function resolveRecord(): Dudika
    {
        return Dudika::firstOrNew([
            'name' => $this->data['name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your dudika import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
