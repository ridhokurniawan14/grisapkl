<?php

namespace App\Filament\Exports;

use App\Models\Teacher;
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

class TeacherExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    private $rowNumber = 0; // Variabel untuk nomor urut

    public function collection()
    {
        return Teacher::all();
    }

    public function map($teacher): array
    {
        $this->rowNumber++; // Tambah 1 setiap baris
        return [
            $this->rowNumber, // Kolom Nomor
            $teacher->name . ($teacher->title ? ', ' . $teacher->title : ''),
            $teacher->nip ?? '-',
            "'" . ($teacher->phone ?? '-'),
            $teacher->subject ?? '-',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT, // Geser ke kolom D karena kolom A jadi Nomor
        ];
    }

    public function headings(): array
    {
        return ['No', 'Nama Guru', 'NIP', 'No. HP', 'Mata Pelajaran'];
    }

    public function styles(Worksheet $sheet)
    {
        // Lebarkan border dari A sampai E
        $sheet->getStyle('A1:E' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
            ],
        ]);

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF16A34A']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
