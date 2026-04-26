<?php

namespace App\Http\Controllers;

use App\Models\Dudika;
use App\Models\PklPlacement;
use App\Models\SchoolProfile;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintController extends Controller
{
    public function suratPengantar($dudika_id)
    {
        $dudika = Dudika::findOrFail($dudika_id);
        $school = SchoolProfile::first();

        // Ambil semua siswa yang PKL di Dudika ini dan statusnya Aktif
        $placements = PklPlacement::with(['student.studentClass', 'assessmentScheme'])
            ->where('dudika_id', $dudika_id)
            ->where('status', 'Aktif')
            ->get();

        if ($placements->isEmpty()) {
            return "Belum ada siswa yang ditempatkan di DUDIKA ini.";
        }

        // Setup PDF (A4 Portrait)
        $pdf = Pdf::loadView('pdf.surat-pengantar', compact('dudika', 'school', 'placements'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Surat_Pengantar_' . $dudika->name . '.pdf');
    }
}
