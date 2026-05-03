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
    public $timeout = 1200;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle(): void
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(1200);

        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$activeYear) return;

        $schedules = \App\Models\MonitoringSchedule::orderBy('id', 'asc')->get();

        // MANTRA SAKTI: Tambahkan 'pklPlacement.dudika' biar query-nya nggak ngos-ngosan
        $monitorings = \App\Models\Monitoring::with(['pklPlacement.teacher', 'pklPlacement.dudika', 'monitoringSchedule'])
            ->whereHas('pklPlacement', function ($q) use ($activeYear) {
                $q->where('academic_year_id', $activeYear->id);
            })
            ->orderBy('date', 'asc')
            ->get();

        $dataGuru = [];
        foreach ($monitorings as $mon) {
            $teacher = $mon->pklPlacement->teacher;
            $dudika = $mon->pklPlacement->dudika; // Ambil DUDIKA-nya sekalian
            if (!$teacher || !$dudika) continue;

            $teacherId = $teacher->id;
            $dudikaId = $dudika->id;
            $schedId = $mon->monitoring_schedule_id;

            // MANTRA SAKTI: Kunci gabungan agar 1 Guru = Banyak Baris (Sesuai jumlah DUDIKA)
            $key = $teacherId . '_' . $dudikaId;

            if (!isset($dataGuru[$key])) {
                $dataGuru[$key] = [
                    'nama_guru' => $teacher->name . (!empty($teacher->title) ? ', ' . $teacher->title : ''),
                    'nama_dudika_utama' => $dudika->name, // Simpan nama dudika untuk kolom pertama
                    'kunjungan' => []
                ];
            }

            if (!isset($dataGuru[$key]['kunjungan'][$schedId])) {
                $fotoPath = null;
                if ($mon->photo_path) {
                    $fullPath = public_path('storage/' . $mon->photo_path);
                    if (file_exists($fullPath)) {
                        $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
                        $fotoPath = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($fullPath));
                    }
                }

                $dataGuru[$key]['kunjungan'][$schedId] = [
                    'tanggal' => \Carbon\Carbon::parse($mon->date)->isoFormat('D MMMM Y'),
                    'foto' => $fotoPath,
                    'nama_dudika' => $dudika->name
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

        $fileName = 'laporan_pkl/Rekap_Monitoring_Guru.pdf';
        Storage::disk('public')->put($fileName, $pdf->output());

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
