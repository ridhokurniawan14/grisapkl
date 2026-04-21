<?php

namespace App\Filament\Exports;

use App\Models\Journal;
use App\Models\PklPlacement;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class JournalExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithDrawings, ShouldAutoSize, WithEvents
{
    protected $ids, $start, $end, $studentId;

    public function __construct(array $ids, $start = null, $end = null, $studentId = null)
    {
        $this->ids = $ids;
        $this->start = $start;
        $this->end = $end;
        $this->studentId = $studentId;
    }

    public function collection()
    {
        $journals = Journal::with(['pklPlacement.student', 'pklPlacement.dudika'])
            ->whereIn('id', $this->ids)->orderBy('date', 'asc')->get();

        // JIKA USER MEMFILTER 1 SISWA SPESIFIK & RANGE TANGGAL
        if ($this->start && $this->end && $this->studentId) {
            $placement = PklPlacement::with(['student', 'dudika'])->where('student_id', $this->studentId)->first();

            if ($placement) {
                $startDate = Carbon::parse($this->start);
                $endDate = Carbon::parse($this->end);
                $journalsKeyed = $journals->keyBy('date');
                $fullCollection = collect();

                // Looping dari tanggal awal sampai tanggal akhir
                for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                    $dateStr = $date->format('Y-m-d');

                    if ($journalsKeyed->has($dateStr)) {
                        $fullCollection->push($journalsKeyed->get($dateStr)); // Masukkan data asli
                    } else {
                        // MANTRA SAKTI: Buat data palsu "Alpha" kalau kosong
                        $dummy = new Journal([
                            'date' => $dateStr,
                            'time' => null,
                            'attend_status' => 'Alpha',
                            'activity' => 'Tanpa Keterangan / Alpha', // REVISI TEKS
                            'is_valid' => true, // REVISI: Dianggap Valid Default
                        ]);
                        $dummy->setRelation('pklPlacement', $placement);
                        $fullCollection->push($dummy);
                    }
                }
                return $fullCollection;
            }
        }

        return $journals;
    }

    public function headings(): array
    {
        return ['HARI/TANGGAL', 'NAMA SISWA', 'TEMPAT PKL', 'WAKTU', 'STATUS', 'URAIAN KEGIATAN', 'VALIDASI', 'FOTO ABSEN', 'FOTO KEGIATAN'];
    }

    public function map($journal): array
    {
        return [
            Carbon::parse($journal->date)->translatedFormat('l, d F Y'),
            $journal->pklPlacement->student->name ?? '-',
            $journal->pklPlacement->dudika->name ?? '-',
            $journal->time ? Carbon::parse($journal->time)->format('H:i') . ' WIB' : '-',
            $journal->attend_status,
            $journal->activity,
            $journal->is_valid ? 'Valid' : ($journal->attend_status == 'Alpha' ? '-' : 'Revisi'),
            '',
            '',
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
        foreach ($this->collection() as $journal) {
            if ($journal->attendance_photo_path && file_exists(storage_path('app/public/' . $journal->attendance_photo_path))) {
                $drawing = new Drawing();
                $drawing->setName('Absen')->setPath(storage_path('app/public/' . $journal->attendance_photo_path))
                    ->setHeight(60)->setCoordinates('H' . $row)->setOffsetX(10)->setOffsetY(10);
                $drawings[] = $drawing;
            }
            if ($journal->photo_path && file_exists(storage_path('app/public/' . $journal->photo_path))) {
                $drawing2 = new Drawing();
                $drawing2->setName('Kegiatan')->setPath(storage_path('app/public/' . $journal->photo_path))
                    ->setHeight(60)->setCoordinates('I' . $row)->setOffsetX(10)->setOffsetY(10);
                $drawings[] = $drawing2;
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

                // MANTRA SAKTI: MEMBERIKAN GARIS BORDER KE SELURUH TABEL
                $sheet->getStyle('A1:I' . $rowCount)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A2:G' . $rowCount)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            },
        ];
    }
}
