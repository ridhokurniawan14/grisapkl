<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateRekapMonitoringJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $timeout = 1200; // Beri waktu 20 menit agar aman dari timeout

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle(): void
    {
        // Beri nafas lega untuk RAM server
        ini_set('memory_limit', '2048M');
        set_time_limit(1200);

        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$activeYear) return;

        $schedules = \App\Models\MonitoringSchedule::orderBy('id', 'asc')->get();

        $monitorings = \App\Models\Monitoring::with(['pklPlacement.teacher', 'monitoringSchedule'])
            ->whereHas('pklPlacement', function ($q) use ($activeYear) {
                $q->where('academic_year_id', $activeYear->id);
            })
            ->orderBy('date', 'asc')
            ->get();

        $dataGuru = [];
        foreach ($monitorings as $mon) {
            $teacher = $mon->pklPlacement->teacher;
            if (!$teacher) continue;

            $teacherId = $teacher->id;
            $schedId = $mon->monitoring_schedule_id;

            if (!isset($dataGuru[$teacherId])) {
                $dataGuru[$teacherId] = [
                    'nama_guru' => $teacher->name . (!empty($teacher->title) ? ', ' . $teacher->title : ''),
                    'kunjungan' => []
                ];
            }

            if (!isset($dataGuru[$teacherId]['kunjungan'][$schedId])) {
                // =========================================================
                // KEMBALI KE BASE64: Karena sudah di Job, ini 100% AMAN & ANTI-LEMOT!
                // =========================================================
                $fotoPath = null;
                if ($mon->photo_path) {
                    $fullPath = public_path('storage/' . $mon->photo_path);
                    if (file_exists($fullPath)) {
                        // Ubah file fisik menjadi Base64 agar DOMPDF pasti bisa membacanya
                        $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
                        $fotoPath = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($fullPath));
                    }
                }

                $dataGuru[$teacherId]['kunjungan'][$schedId] = [
                    'tanggal' => \Carbon\Carbon::parse($mon->date)->isoFormat('D MMMM Y'),
                    'foto' => $fotoPath
                ];
            }
        }

        $pdf = Pdf::loadView('pdf.rekap_monitoring', compact('activeYear', 'schedules', 'dataGuru'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'enable_font_subsetting' => false,
                'dpi' => 72
            ]);

        // Simpan 1 file global untuk di-download
        $fileName = 'laporan_pkl/Rekap_Monitoring_Guru.pdf';
        Storage::disk('public')->put($fileName, $pdf->output());

        // Kirim Notif ke Lonceng Filament!
        $user = \App\Models\User::find($this->userId);
        if ($user) {
            \Filament\Notifications\Notification::make()
                ->title('Generate Rekap Selesai!')
                ->body('File Rekap Monitoring Guru sudah berhasil dibuat dan siap diunduh.')
                ->success()
                ->sendToDatabase($user);
        }
    }
}
