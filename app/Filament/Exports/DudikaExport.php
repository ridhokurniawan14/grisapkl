<?php

namespace App\Filament\Exports;

use App\Models\Dudika;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DudikaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    public function collection()
    {
        return Dudika::all();
    }

    public function map($dudika): array
    {
        return [
            $dudika->name,
            $dudika->address ?? '-',
            $dudika->head_name ?? '-',
            $dudika->head_nip ?? '-',
            $dudika->supervisor_name ?? '-',
            "'" . $dudika->supervisor_phone ?? '-',
            $dudika->is_complete ? 'Lengkap' : 'Belum Lengkap',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_TEXT, // kolom No. HP Pembimbing
        ];
    }

    // Mengatur Judul Kolom (Header)
    public function headings(): array
    {
        return [
            'Nama DUDIKA',
            'Alamat',
            'Nama Pimpinan',
            'NIP Pimpinan',
            'Nama Pembimbing',
            'No. HP Pembimbing',
            'Status Data'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 1. Border semua cell
        $sheet->getStyle('A1:G' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // 2. Style header row
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF16A34A'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
