<?php

namespace App\Filament\Exports;

use App\Models\PklPlacement;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class PklPlacementExporter extends Exporter
{
    protected static ?string $model = PklPlacement::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('academicYear.name')->label('Tahun Ajaran'),
            ExportColumn::make('student.nis')->label('NIS Siswa'),
            ExportColumn::make('student.name')->label('Nama Siswa'),
            ExportColumn::make('student.studentClass.name')->label('Kelas Siswa'),
            ExportColumn::make('dudika.name')->label('Nama DUDIKA'),
            ExportColumn::make('dudika.address')->label('Alamat DUDIKA'), // <-- KOLOM BARU
            ExportColumn::make('teacher.name')->label('Guru Pembimbing (Sekolah)'),
            ExportColumn::make('dudika.supervisor_name')->label('Pembimbing Lapangan (DUDIKA)'), // <-- KOLOM BARU
            ExportColumn::make('start_date')->label('Tanggal Mulai'),
            ExportColumn::make('end_date')->label('Tanggal Selesai'),
            ExportColumn::make('latitude')->label('Latitude'),
            ExportColumn::make('longitude')->label('Longitude'),
            ExportColumn::make('status')->label('Status PKL'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Data Penempatan PKL anda berhasil diekspor. ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
