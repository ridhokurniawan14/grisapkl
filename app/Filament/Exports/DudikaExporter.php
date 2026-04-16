<?php

namespace App\Exports;

use App\Models\Dudika;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DudikaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Dudika::all();
    }

    // Mengatur isi data per baris
    public function map($dudika): array
    {
        return [
            $dudika->name,
            $dudika->address ?? '-',
            $dudika->head_name ?? '-',
            $dudika->head_nip ?? '-',
            $dudika->supervisor_name ?? '-',
            $dudika->supervisor_phone ?? '-',
            $dudika->is_complete ? 'Lengkap' : 'Belum Lengkap',
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

    // MENGATUR DESAIN (WARNA & BORDER)
    public function styles(Worksheet $sheet)
    {
        // 1. Beri Border ke semua cell yang ada isinya
        $sheet->getStyle('A1:G' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'], // Border hitam
                ],
            ],
        ]);

        // 2. Warnai Baris Pertama (Header) jadi Hijau Teks Putih
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF16A34A'] // Warna Hijau
                ],
            ],
        ];
    }
}
