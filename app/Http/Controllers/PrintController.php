<?php

namespace App\Http\Controllers;

use App\Models\Dudika;
use App\Models\PklPlacement;
use App\Models\SchoolProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
    public function cetakLaporanLengkap($id)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $placement = \App\Models\PklPlacement::with([
            'student.studentClass.major',
            'dudika',
            'teacher',
            'journals' => fn($q) => $q->orderBy('date', 'asc')
        ])->findOrFail($id);

        $school = \App\Models\SchoolProfile::first();

        // ===================================================================
        // MANTRA SAKTI 1: ENKRIPSI ID AGAR TIDAK BISA DITEBAK (HASHING)
        // ===================================================================
        $hashedId = \Illuminate\Support\Facades\Crypt::encryptString($placement->id);
        $qrUrl = url('/verifikasi/laporan/' . $hashedId);

        $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->errorCorrection('H')
            ->size(90)
            ->margin(1)
            ->generate($qrUrl));

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.laporan.master', compact('placement', 'school', 'qrCode'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                // ===================================================================
                // MANTRA SAKTI 2: IZINKAN PHP AGAR FOOTER HALAMAN MUNCUL!
                // ===================================================================
                'isPhpEnabled' => true,
            ]);

        return $pdf->stream('Laporan_PKL_' . $placement->student->name . '.pdf');
    }
}
