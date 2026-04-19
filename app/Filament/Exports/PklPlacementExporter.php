<?php

namespace App\Filament\Exports;

use App\Models\PklPlacement;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PklPlacementExporter extends Exporter
{
    protected static ?string $model = PklPlacement::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('academicYear.name')->label('Tahun Ajaran'),
            ExportColumn::make('student.nis')->label('NIS Siswa'),
            ExportColumn::make('student.name')->label('Nama Siswa'),

            // ANTI-ERROR: Ambil data kelas dengan aman
            ExportColumn::make('kelas_siswa')
                ->label('Kelas Siswa')
                ->state(fn($record) => $record->student?->studentClass?->name ?? '-'),

            ExportColumn::make('dudika.name')->label('Nama DUDIKA'),

            // ANTI-ERROR: Ambil alamat DUDIKA dengan aman
            ExportColumn::make('dudika_address')
                ->label('Alamat DUDIKA')
                ->state(fn($record) => $record->dudika?->address ?? '-'),

            ExportColumn::make('teacher.name')->label('Guru Pembimbing (Sekolah)'),

            // ANTI-ERROR: Ambil pembimbing lapangan dengan aman
            ExportColumn::make('pembimbing_dudika')
                ->label('Pembimbing Lapangan (DUDIKA)')
                ->state(fn($record) => $record->dudika?->supervisor_name ?? '-'),

            ExportColumn::make('start_date')->label('Tanggal Mulai'),
            ExportColumn::make('end_date')->label('Tanggal Selesai'),
            ExportColumn::make('latitude')->label('Latitude'),
            ExportColumn::make('longitude')->label('Longitude'),
            ExportColumn::make('status')->label('Status PKL'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor data penempatan PKL telah selesai dan 100% berhasil.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' Namun ada ' . number_format($failedRowsCount) . ' baris yang gagal diekspor.';
        }

        return $body;
    }
}
