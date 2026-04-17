<?php

namespace App\Filament\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StudentExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    use Exportable;

    private $rowNumber = 0;
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function map($student): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,                                         // A
            $student->name,                                           // B
            $student->nis ?? '-',                                     // C
            $student->nisn ?? '-',                                    // D
            $student->gender ?? '-',                                  // E
            $student->studentClass->name ?? '-',                      // F
            $student->academicYear->name ?? '-',                      // G
            $student->birth_place ?? '-',                             // H
            $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d/m/Y') : '-', // I
            "'" . ($student->phone ?? '-'),                           // J (No HP Siswa)
            $student->address ?? '-',                                 // K
            $student->father_name ?? '-',                             // L
            $student->father_job ?? '-',                              // M
            $student->mother_name ?? '-',                             // N
            $student->mother_job ?? '-',                              // O
            "'" . ($student->parent_phone ?? '-'),                    // P (No HP Ortu)
            $student->parent_address ?? '-',                          // Q
            $student->is_complete ? 'Lengkap' : 'Belum Lengkap',      // R
        ];
    }

    // NAMA METHOD HARUS TEPAT: columnFormats
    public function columnFormats(): array
    {
        return [
            'J' => NumberFormat::FORMAT_TEXT, // No HP Siswa
            'P' => NumberFormat::FORMAT_TEXT, // No HP Ortu
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'NIS',
            'NISN',
            'L/P',
            'Kelas',
            'Tahun Ajaran',
            'Tempat Lahir',
            'Tgl Lahir',
            'No. HP Siswa',
            'Alamat Lengkap',
            'Nama Ayah',
            'Pekerjaan Ayah',
            'Nama Ibu',
            'Pekerjaan Ibu',
            'No. HP Orang Tua',
            'Alamat Orang Tua',
            'Status Data'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Sesuaikan range border sampai kolom R
        $sheet->getStyle('A1:R' . $sheet->getHighestRow())->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
        ]);

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF16A34A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }
}
