<?php

namespace App\Filament\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class UnuploadedJournalExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $query;
    private $rowNumber = 0;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        // Execute query lalu URUTKAN berdasarkan Nama Siswa (A-Z), lalu Tanggal
        return $this->query->get()->sortBy([
            ['pklPlacement.student.name', 'asc'],
            ['date', 'asc'],
        ]);
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'Nama Pembimbing', // Tambahan Kolom Pembimbing
            'Tempat PKL',
            'Tanggal',
            'Status',
            'Keterangan',
        ];
    }

    public function map($journal): array
    {
        $this->rowNumber++;

        // CEK RELASI PEMBIMBING
        // Sesuaikan kata "teacher" atau "advisor" dengan nama fungsi relasi di model PklPlacement kamu
        $pembimbing = $journal->pklPlacement->teacher->name
            ?? $journal->pklPlacement->advisor->name
            ?? 'Belum Diatur';

        return [
            $this->rowNumber,
            $journal->pklPlacement->student->name ?? '-',
            $pembimbing,
            $journal->pklPlacement->dudika->name ?? '-',
            Carbon::parse($journal->date)->format('d M Y'),
            $journal->attend_status,
            'Belum Upload Foto',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Deteksi batas baris dan kolom yang terisi data
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // 1. STYLE UNTUK HEADER (Baris 1)
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // Teks Putih
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FF10B981'], // Background Hijau Filament
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // 2. BERI BORDER UNTUK SEMUA CELL (Dari A1 sampai Ujung Data)
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'], // Border Hitam
                ],
            ],
        ]);

        // 3. Bikin Teks Rata Tengah untuk kolom No (A), Tanggal (E), Status (F)
        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E2:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
