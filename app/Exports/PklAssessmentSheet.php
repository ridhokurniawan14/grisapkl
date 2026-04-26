<?php

namespace App\Exports;

use App\Models\PklAssessment;
use App\Models\AssessmentIndicator;
use App\Models\AcademicYear;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate; // <-- WAJIB IMPORT INI

class PklAssessmentSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithCustomStartCell
{
    protected $schemeId;
    protected $schemeName;
    protected $groupedIndicators;
    protected $rowNumber = 0;
    protected $activeYear;

    public function __construct($schemeId, $schemeName)
    {
        $this->schemeId = $schemeId;
        $this->schemeName = substr(str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', $schemeName), 0, 31);

        // MANTRA SAKTI 1: Tarik data dan LANGSUNG dikelompokkan per Elemen
        $indicators = AssessmentIndicator::where('assessment_scheme_id', $this->schemeId)
            ->where('is_active', true)
            ->with('assessmentElement')
            ->orderBy('assessment_element_id')
            ->get();

        $this->groupedIndicators = $indicators->groupBy('assessment_element_id');
        $this->activeYear = AcademicYear::where('is_active', true)->first()?->name ?? '-';
    }

    public function startCell(): string
    {
        return 'A3'; // Baris 1 & 2 dipakai untuk Judul Besar
    }

    public function query()
    {
        return PklAssessment::query()
            ->whereHas('pklPlacement', function ($q) {
                $q->where('assessment_scheme_id', $this->schemeId);
            })
            ->with(['pklPlacement.student.studentClass', 'pklPlacement.dudika', 'pklPlacement.teacher', 'scores']);
    }

    public function title(): string
    {
        return $this->schemeName;
    }

    // =========================================================
    // MANTRA SAKTI 2: BIKIN HEADER 2 BARIS (BERTINGKAT)
    // =========================================================
    public function headings(): array
    {
        // Baris Atas (Untuk Judul Elemen)
        $row1 = ['NO', 'KELAS', 'NAMA SISWA', 'L/P', 'TEMPAT PKL', 'ALAMAT DUDIKA', 'GURU PEMBIMBING'];
        // Baris Bawah (Untuk Judul Indikator)
        $row2 = ['NO', 'KELAS', 'NAMA SISWA', 'L/P', 'TEMPAT PKL', 'ALAMAT DUDIKA', 'GURU PEMBIMBING'];

        foreach ($this->groupedIndicators as $elementId => $inds) {
            $elementName = $inds->first()->assessmentElement->name ?? 'Elemen';

            $row1[] = $elementName; // Masukkan Judul Elemen

            foreach ($inds as $idx => $ind) {
                if ($idx > 0) {
                    $row1[] = ''; // Kasih sel kosong untuk ruang Merge Header Elemen
                }
                $row2[] = $ind->name; // Masukkan Judul Indikator di baris bawahnya
            }

            // Tambahkan kolom RATA2 di akhir setiap elemen
            $row1[] = ''; // Sel kosong untuk Merge
            $row2[] = 'RATA2';
        }

        return [$row1, $row2];
    }

    // =========================================================
    // MANTRA SAKTI 3: INPUT NILAI & HITUNG RATA-RATA PER ELEMEN
    // =========================================================
    public function map($assessment): array
    {
        $this->rowNumber++;
        $placement = $assessment->pklPlacement;

        $row = [
            $this->rowNumber,
            $placement->student->studentClass->name ?? '-',
            $placement->student->name ?? '-',
            $placement->student->gender ?? '-',
            $placement->dudika->name ?? '-',
            $placement->dudika->address ?? '-',
            $placement->teacher->name ?? '-',
        ];

        $scores = $assessment->scores->keyBy('assessment_indicator_id');

        // Looping per Elemen
        foreach ($this->groupedIndicators as $elementId => $inds) {
            $totalScore = 0;
            $count = 0;

            // Looping Indikator
            foreach ($inds as $indicator) {
                if ($scores->has($indicator->id)) {
                    $nilai = $scores[$indicator->id]->score;
                    $row[] = $nilai;
                    $totalScore += $nilai;
                    $count++;
                } else {
                    $row[] = 0;
                }
            }

            // Hitung dan masukkan Rata-rata khusus untuk elemen ini
            $row[] = $count > 0 ? round($totalScore / $count, 2) : 0;
        }

        return $row;
    }

    // =========================================================
    // MANTRA SAKTI 4: STYLING, MERGE & WARNA KUNING
    // =========================================================
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                // 1. Tambah Judul Besar di Baris 1 & 2
                $sheet->setCellValue('A1', "REKAP PENILAIAN DATA SISWA PKL - SKEMA: " . strtoupper($this->schemeName));
                $sheet->setCellValue('A2', "TAHUN AJARAN: " . strtoupper($this->activeYear));
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->getStyle("A1:A2")->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle("A1:A2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 2. Merge Vertikal Kolom Statis (A3:A4, B3:B4, dst)
                for ($i = 1; $i <= 7; $i++) {
                    $colLetter = Coordinate::stringFromColumnIndex($i);
                    $sheet->mergeCells("{$colLetter}3:{$colLetter}4");
                }

                // 3. Warna Biru untuk Kolom Statis (Kiri)
                $sheet->getStyle("A3:G4")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '5B9BD5']], // Biru Soft
                    'font' => ['color' => ['rgb' => 'FFFFFF']]
                ]);

                // 4. Merge Header Elemen & Warna Kuning
                $startColIndex = 8; // Kolom ke-8 (H) adalah awal indikator
                foreach ($this->groupedIndicators as $elementId => $inds) {
                    $colCount = count($inds) + 1; // +1 untuk kolom RATA2
                    $endColIndex = $startColIndex + $colCount - 1;

                    $startColLetter = Coordinate::stringFromColumnIndex($startColIndex);
                    $endColLetter = Coordinate::stringFromColumnIndex($endColIndex);

                    // Merge Judul Elemen secara horizontal
                    $sheet->mergeCells("{$startColLetter}3:{$endColLetter}3");

                    // Warna Kuning u/ Judul Elemen (Baris 3)
                    $sheet->getStyle("{$startColLetter}3:{$endColLetter}3")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']],
                        'font' => ['color' => ['rgb' => '000000']]
                    ]);

                    // Warna Kuning u/ Keseluruhan Kolom RATA2 (Dari atas ke bawah)
                    $sheet->getStyle("{$endColLetter}3:{$endColLetter}{$highestRow}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']],
                        'font' => ['bold' => true, 'color' => ['rgb' => '000000']]
                    ]);

                    $startColIndex += $colCount; // Geser start ke elemen berikutnya
                }

                // 5. Border & Alignment (Wrap Text biar rapi)
                $range = "A3:{$lastColumn}{$highestRow}";
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);

                // Center Header & Data Numerik
                $sheet->getStyle("A3:{$lastColumn}4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A3:{$lastColumn}4")->getFont()->setBold(true);
                $sheet->getStyle("A5:B{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D5:D{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H5:{$lastColumn}{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
