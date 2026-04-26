<?php

namespace App\Exports;

use App\Models\AssessmentScheme;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PklAssessmentsExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $sheets = [];

        // Ambil semua Skema Penilaian yang aktif
        $schemes = AssessmentScheme::where('is_active', true)->get();

        // Buatkan 1 Sheet untuk setiap Skema
        foreach ($schemes as $scheme) {
            $sheets[] = new PklAssessmentSheet($scheme->id, $scheme->name);
        }

        return $sheets;
    }
}
