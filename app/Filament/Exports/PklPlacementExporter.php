<?php

namespace App\Filament\Exports;

use App\Models\PklPlacement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PklPlacementExporter implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $query;

    // Kita butuh construct ini biar hasil download-nya sesuai sama filter jurusan/tahun yang diklik Humas
    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'TAHUN AJARAN',
            'NIS',
            'NAMA SISWA',
            'KELAS',
            'TEMPAT DUDIKA',
            'ALAMAT DUDIKA',
            'GURU PEMBIMBING',
            'PEMBIMBING DUDIKA',
            'TANGGAL MULAI',
            'TANGGAL SELESAI',
            'STATUS',
            'LATITUDE',
            'LONGITUDE'
        ];
    }

    public function map($record): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $record->academicYear?->name ?? '-',
            "'" . $record->student?->nis,
            $record->student?->name ?? '-',
            $record->student?->studentClass?->name ?? '-',
            $record->dudika?->name ?? '-',
            $record->dudika?->address ?? '-',
            $record->teacher?->name ?? '-',
            $record->dudika?->supervisor_name ?? '-',

            // OBAT ANTI-ERROR: Ubah string jadi Carbon dulu sebelum di-format
            $record->start_date ? \Carbon\Carbon::parse($record->start_date)->format('d-m-Y') : '-',
            $record->end_date ? \Carbon\Carbon::parse($record->end_date)->format('d-m-Y') : '-',

            $record->status,
            $record->latitude,
            $record->longitude,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Border seluruh sel
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:N' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Style Header
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF16A34A'], // Warna hijau
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Rata tengah untuk kolom No, NIS, Tanggal, Status
            'A' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'C' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'J' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'K' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'L' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
        ];
    }
}
