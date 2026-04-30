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
    public function cetakLaporanLengkap($id)
    {
        // Alokasikan memori khusus untuk cetak laporan tebal
        ini_set('memory_limit', '1024M'); // Naikkan jadi 1GB untuk amannya
        set_time_limit(600); // 10 menit maksimal eksekusi

        // MANTRA SAKTI OPTIMASI: Eager Load sedalam-dalamnya!
        $placement = \App\Models\PklPlacement::with([
            'student.studentClass.major',
            'dudika',
            'teacher',
            // Load jurnal sekaligus bawa relasi yang nempel di jurnal
            'journals' => function ($q) {
                $q->with(['pklPlacement.student', 'pklPlacement.dudika'])->orderBy('date', 'asc');
            }
        ])->findOrFail($id);

        // Render sekolah sekali saja (jangan di-query berulang-ulang)
        $school = \App\Models\SchoolProfile::first();

        // Enkripsi Hashed ID
        $hashedId = \Illuminate\Support\Facades\Crypt::encryptString($placement->id);
        $qrUrl = url('/verifikasi/laporan/' . $hashedId);

        // Format QR SVG
        $qrCode = base64_encode(
            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->errorCorrection('M')
                ->size(200)
                ->margin(2)
                ->generate($qrUrl)
        );

        // MANTRA SAKTI 2: Gunakan Cache untuk mempercepat DOMPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.laporan.master', compact('placement', 'school', 'qrCode'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true,
                'enable_font_subsetting' => true, // Mempercepat render font
                'dpi' => 96 // Turunkan sedikit DPI agar render lebih cepat
            ]);

        return $pdf->stream('Laporan_PKL_' . $placement->student->name . '.pdf');
    }
}
