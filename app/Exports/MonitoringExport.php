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

    // Cache collection biar tidak query berkali-kali (drawings + events butuh ini)
    protected $cachedCollection = null;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        if ($this->cachedCollection) {
            return $this->cachedCollection;
        }

        // ✅ Ambil hanya 1 record per kunjungan DUDIKA (MIN id, sama seperti logika tabel)
        $representativeIds = Monitoring::selectRaw('MIN(monitorings.id) as id')
            ->join('pkl_placements', 'pkl_placements.id', '=', 'monitorings.pkl_placement_id')
            ->whereIn('monitorings.id', $this->ids)
            ->groupBy(
                'pkl_placements.dudika_id',
                'monitorings.date',
                'monitorings.monitoring_schedule_id'
            )
            ->pluck('id');

        $this->cachedCollection = Monitoring::with([
            'pklPlacement.student',
            'pklPlacement.dudika',
            'pklPlacement.teacher',
        ])
            ->whereIn('id', $representativeIds)
            ->orderBy('date', 'desc')
            ->get();

        return $this->cachedCollection;
    }

    public function headings(): array
    {
        return [
            'TANGGAL',
            'WAKTU',
            'GURU PEMBIMBING',
            'NAMA SISWA',           // Akan berisi semua siswa (multi-line dalam 1 cell)
            'TEMPAT PKL (DUDIKA)',
            'KEGIATAN / CATATAN',
            'FOTO KUNJUNGAN',
        ];
    }

    public function map($monitoring): array
    {
        // ✅ Ambil semua siswa dari kunjungan DUDIKA yang sama
        $students = Monitoring::where('date', $monitoring->date)
            ->where('monitoring_schedule_id', $monitoring->monitoring_schedule_id)
            ->whereHas('pklPlacement', fn($q) => $q->where(
                'dudika_id',
                $monitoring->pklPlacement->dudika_id
            ))
            ->with('pklPlacement.student')
            ->get()
            ->map(fn($m) => $m->pklPlacement?->student?->name)
            ->filter()
            ->values()
            ->map(fn($name, $i) => ($i + 1) . '. ' . $name)
            ->implode("\n"); // ✅ Newline di Excel (wrap text)

        return [
            \Carbon\Carbon::parse($monitoring->date)->translatedFormat('d F Y'),
            $monitoring->time,
            $monitoring->pklPlacement->teacher->name ?? '-',
            $students ?: '-',
            $monitoring->pklPlacement->dudika->name ?? '-',
            $monitoring->activity,
            '', // Slot foto
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color'    => ['argb' => 'FF0070C0'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function drawings()
    {
        $drawings = [];
        $row      = 2;

        foreach ($this->collection() as $monitoring) {
            $path = storage_path('app/public/' . $monitoring->photo_path);

            if ($monitoring->photo_path && file_exists($path)) {
                $drawing = new Drawing();
                $drawing->setName('Foto Kunjungan');
                $drawing->setPath($path);
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
                $sheet    = $event->sheet->getDelegate();
                $rowCount = $this->collection()->count() + 1;

                for ($i = 2; $i <= $rowCount; $i++) {
                    // ✅ Tinggi baris otomatis proporsional dengan jumlah siswa
                    $monitoring   = $this->collection()->get($i - 2);
                    $studentCount = $monitoring
                        ? Monitoring::where('date', $monitoring->date)
                        ->where('monitoring_schedule_id', $monitoring->monitoring_schedule_id)
                        ->whereHas('pklPlacement', fn($q) => $q->where(
                            'dudika_id',
                            $monitoring->pklPlacement->dudika_id
                        ))
                        ->count()
                        : 1;

                    // Min 65px, tambah 18px per siswa agar nama tidak terpotong
                    $rowHeight = max(65, $studentCount * 18);
                    $sheet->getRowDimension($i)->setRowHeight($rowHeight);
                }

                // Border semua sel
                $sheet->getStyle('A1:G' . $rowCount)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // ✅ Wrap text kolom D (Nama Siswa) biar newline "\n" tampil dengan benar
                $sheet->getStyle('D2:D' . $rowCount)
                    ->getAlignment()
                    ->setWrapText(true);

                $sheet->getStyle('A2:F' . $rowCount)
                    ->getAlignment()
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            },
        ];
    }
}
