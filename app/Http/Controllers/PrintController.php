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
        ini_set('memory_limit', '1024M');
        set_time_limit(600);

        $placement = \App\Models\PklPlacement::with([
            'student.studentClass.major',
            'dudika',
            'teacher',
            'monitorings' => fn($q) => $q->orderBy('date', 'asc'),
            'journals' => fn($q) => $q->orderBy('date', 'asc')
        ])->findOrFail($id);

        // ===================================================================
        // MANTRA SAKTI: INJECT LOGIKA JURNAL ALPHA (DARI ROUTE LAMA)
        // ===================================================================
        $startDate = \Carbon\Carbon::parse($placement->start_date);
        $endDate = \Carbon\Carbon::parse($placement->end_date);
        $journalsKeyed = $placement->journals->keyBy('date');
        $studentJournals = collect();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            if ($journalsKeyed->has($dateStr)) {
                $studentJournals->push($journalsKeyed->get($dateStr));
            } else {
                // Buat dummy journal jika kosong
                $dummy = new \App\Models\Journal([
                    'date'          => $dateStr,
                    'time'          => null,
                    'attend_status' => 'Alpha',
                    'activity'      => 'Tanpa Keterangan / Alpha',
                    'is_valid'      => true,
                ]);
                $studentJournals->push($dummy);
            }
        }
        // Timpa relasi journals bawaan dengan collection yang sudah lengkap
        $placement->setRelation('journals', $studentJournals);
        // ===================================================================

        $school = \App\Models\SchoolProfile::first();
        $hashedId = \Illuminate\Support\Facades\Crypt::encryptString($placement->id);
        $qrUrl = url('/verifikasi/laporan/' . $hashedId);

        $qrCode = base64_encode(
            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->errorCorrection('M')
                ->size(200)
                ->margin(2)
                ->generate($qrUrl)
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.laporan.master', compact('placement', 'school', 'qrCode'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true,
                'enable_font_subsetting' => true,
                'dpi' => 96
            ]);

        return $pdf->stream('Laporan_PKL_' . $placement->student->name . '.pdf');
    }
    public function cetakRekapMonitoring()
    {
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$activeYear) return abort(404, 'Tahun ajaran aktif tidak ditemukan.');

        // 1. Ambil semua jadwal dinamis (Untuk Header Kolom)
        $schedules = \App\Models\MonitoringSchedule::orderBy('id', 'asc')->get();

        // 2. Ambil semua data kunjungan di tahun aktif
        $monitorings = \App\Models\Monitoring::with(['pklPlacement.teacher', 'monitoringSchedule'])
            ->whereHas('pklPlacement', function ($q) use ($activeYear) {
                $q->where('academic_year_id', $activeYear->id);
            })
            ->orderBy('date', 'asc')
            ->get();

        // 3. Kelompokkan Data (Pivot) ke masing-masing Guru
        $dataGuru = [];
        foreach ($monitorings as $mon) {
            $teacher = $mon->pklPlacement->teacher;
            if (!$teacher) continue; // Skip kalau tidak ada guru

            $teacherId = $teacher->id;
            $schedId = $mon->monitoring_schedule_id;

            // Jika guru belum ada di array, daftarkan
            if (!isset($dataGuru[$teacherId])) {
                $dataGuru[$teacherId] = [
                    'nama_guru' => $teacher->name . (!empty($teacher->title) ? ', ' . $teacher->title : ''),
                    'kunjungan' => []
                ];
            }

            // Masukkan data kunjungan ke kolom jadwal yang sesuai (Ambil 1 foto representatif per jadwal)
            if (!isset($dataGuru[$teacherId]['kunjungan'][$schedId])) {
                $fotoBase64 = null;
                if ($mon->photo_path) {
                    $fullPath = public_path('storage/' . $mon->photo_path);
                    if (file_exists($fullPath) && filesize($fullPath) < 2000000) { // Aman dari RAM bocor (max 2MB)
                        $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
                        $fotoBase64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($fullPath));
                    }
                }

                $dataGuru[$teacherId]['kunjungan'][$schedId] = [
                    'tanggal' => \Carbon\Carbon::parse($mon->date)->isoFormat('D MMMM Y'),
                    'foto' => $fotoBase64
                ];
            }
        }

        // Render ke PDF (Landscape agar kolom luas)
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.rekap_monitoring', compact('activeYear', 'schedules', 'dataGuru'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'enable_font_subsetting' => false, // Wajib false biar cepat
                'dpi' => 72
            ]);

        return $pdf->stream('Rekap_Monitoring_Guru.pdf');
    }
}
