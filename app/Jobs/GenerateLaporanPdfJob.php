<?php

namespace App\Jobs;

use App\Models\PklPlacement;
use App\Models\SchoolProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateLaporanPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $placementId;
    public $userId;
    public $timeout = 900;

    public function __construct($placementId, $userId)
    {
        $this->placementId = $placementId;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        // Naikkan limit & paksa GC bersih sebelum mulai
        ini_set('memory_limit', '1024M');
        gc_enable();
        gc_collect_cycles();

        try {
            $placement = PklPlacement::with([
                'student.studentClass.major',
                'dudika',
                'teacher',
                'monitorings' => fn($q) => $q->orderBy('date', 'asc'),
                'journals' => function ($q) {
                    $q->with(['pklPlacement.student', 'pklPlacement.dudika'])
                        ->orderBy('date', 'asc');
                },
                'pklAssessment.scores.assessmentIndicator.assessmentElement'
            ])->find($this->placementId);

            if (!$placement) return;

            // LOGIKA JURNAL ALPHA (sama seperti sebelumnya)
            $startDate = \Carbon\Carbon::parse($placement->start_date);
            $endDate   = \Carbon\Carbon::parse($placement->end_date);
            $journalsKeyed = $placement->journals->keyBy('date');
            $studentJournals = collect();

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dateStr = $date->format('Y-m-d');
                if ($journalsKeyed->has($dateStr)) {
                    $studentJournals->push($journalsKeyed->get($dateStr));
                } else {
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
            $placement->setRelation('journals', $studentJournals);

            $school     = SchoolProfile::first();
            $hashedId   = \Illuminate\Support\Facades\Crypt::encryptString($placement->id);
            $qrUrl      = url('/verifikasi/laporan/' . $hashedId);

            $qrCode = base64_encode(
                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                    ->errorCorrection('M')
                    ->size(200)
                    ->margin(2)
                    ->generate($qrUrl)
            );

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'pdf.laporan.master',
                compact('placement', 'school', 'qrCode')
            )
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => false, // ← matikan, tidak perlu remote
                    'isPhpEnabled'         => true,
                    'enable_font_subsetting' => false,
                    'dpi'                  => 72,
                    'defaultFont'          => 'sans-serif',
                ]);

            $pdfOutput = $pdf->output();

            $fileName = 'laporan_pkl/Laporan_'
                . \Illuminate\Support\Str::slug($placement->student->name)
                . '_' . time() . '.pdf';

            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $pdfOutput);
            $placement->update(['file_laporan_path' => $fileName]);

            // Bebaskan memory setelah PDF selesai
            unset($pdf, $pdfOutput, $studentJournals, $placement, $qrCode);
            gc_collect_cycles();

            // KIRIM NOTIFIKASI
            $user = \App\Models\User::find($this->userId);
            if ($user) {
                \Filament\Notifications\Notification::make()
                    ->title('Generate Laporan Selesai!')
                    ->body('Laporan PKL sudah siap diunduh.')
                    ->success()
                    ->sendToDatabase($user);
            }
        } catch (\Exception $e) {
            $placement = PklPlacement::find($this->placementId);
            if ($placement) {
                $placement->update(['file_laporan_path' => null]);
            }
            \Illuminate\Support\Facades\Log::error('Gagal Generate Laporan: ' . $e->getMessage(), [
                'placement_id' => $this->placementId,
                'trace'        => $e->getTraceAsString(),
            ]);

            throw $e; // ← lempar ulang biar queue catat sebagai failed job
        }
    }
}
