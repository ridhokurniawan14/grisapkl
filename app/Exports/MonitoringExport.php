<?php

namespace App\Exports;

use App\Models\Monitoring;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MonitoringExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithDrawings, ShouldAutoSize, WithEvents
{
    protected $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        return Monitoring::with(['pklPlacement.student', 'pklPlacement.dudika', 'pklPlacement.teacher'])
            ->whereIn('id', $this->ids)->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'TANGGAL',
            'WAKTU',
            'GURU PEMBIMBING',
            'NAMA SISWA',
            'TEMPAT PKL (DUDIKA)',
            'KEGIATAN / CATATAN',
            'FOTO KUNJUNGAN'
        ];
    }

    public function map($monitoring): array
    {
        return [
            \Carbon\Carbon::parse($monitoring->date)->translatedFormat('d F Y'),
            $monitoring->time,
            $monitoring->pklPlacement->teacher->name ?? '-',
            $monitoring->pklPlacement->student->name ?? '-',
            $monitoring->pklPlacement->dudika->name ?? '-',
            $monitoring->activity,
            '', // Slot untuk foto
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FF0070C0']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }

    public function drawings()
    {
        $drawings = [];
        $row = 2;
        foreach ($this->collection() as $monitoring) {
            if ($monitoring->photo_path && file_exists(storage_path('app/public/' . $monitoring->photo_path))) {
                $drawing = new Drawing();
                $drawing->setName('Foto Kunjungan');
                $drawing->setPath(storage_path('app/public/' . $monitoring->photo_path));
                $drawing->setHeight(60);
                $drawing->setCoordinates('G' . $row);
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(10);
                $drawings[] = $drawing;
            }
            $row++;
        }
        return $drawings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = $this->collection()->count() + 1;

                for ($i = 2; $i <= $rowCount; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(65);
                }

                // Border semua sel
                $sheet->getStyle('A1:G' . $rowCount)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A2:F' . $rowCount)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            },
        ];
    }
}
