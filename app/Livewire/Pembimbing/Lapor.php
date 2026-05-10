<?php

namespace App\Livewire\Pembimbing;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Teacher;
use App\Models\PklPlacement;
use App\Models\MonitoringSchedule;
use App\Models\Monitoring;

#[Layout('components.layouts.app')]
#[Title('Lapor Monitoring - GrisaPKL')]
class Lapor extends Component
{
    public $filterMonth;

    public function mount()
    {
        $this->filterMonth = Carbon::now()->format('Y-m');
    }

    public function render()
    {
        $user  = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        // ── 1. JADWAL AKTIF ──────────────────────────────────────────────────
        $activeSchedule = MonitoringSchedule::where('is_active', 1)->latest()->first();

        $isActiveWindow  = false;
        $scheduleName    = 'TIDAK ADA JADWAL AKTIF';
        $scheduleDateStr = '-';
        $activeScheduleId = null;

        $totalDudika = 0;
        $visited     = 0;
        $remaining   = 0;
        $historyData = collect();

        if ($teacher) {

            // ── 2. AMBIL SEMUA PLACEMENT IDS MILIK GURU INI ─────────────────
            //    Ini adalah "jembatan" karena monitorings tidak punya teacher_id
            $placements = PklPlacement::where('teacher_id', $teacher->id)
                ->where('status', 'Aktif')
                ->get(['id', 'dudika_id']);

            $placementIds = $placements->pluck('id');

            // Hitung total unik DUDIKA yang dibimbing
            $totalDudika = $placements->pluck('dudika_id')->filter()->unique()->count();

            // ── 3. DATA JADWAL & WINDOW AKTIF ───────────────────────────────
            if ($activeSchedule) {
                $activeScheduleId = $activeSchedule->id;
                $scheduleName     = strtoupper($activeSchedule->name);
                $start = Carbon::parse($activeSchedule->start_date);
                $end   = Carbon::parse($activeSchedule->end_date);

                $scheduleDateStr = $start->isoFormat('D MMM YYYY') . ' - ' . $end->isoFormat('D MMM YYYY');
                $isActiveWindow  = Carbon::now()->betweenIncluded($start->startOfDay(), $end->endOfDay());

                // ── 4. HITUNG VISITED (unik DUDIKA yang sudah dimonitoring) ─
                //    FIX: whereIn pkl_placement_id, bukan where teacher_id
                //    JOIN ke pkl_placements untuk dapat dudika_id-nya
                $visited = Monitoring::where('monitoring_schedule_id', $activeScheduleId)
                    ->whereIn('pkl_placement_id', $placementIds)
                    ->join('pkl_placements', 'monitorings.pkl_placement_id', '=', 'pkl_placements.id')
                    ->distinct('pkl_placements.dudika_id')
                    ->count('pkl_placements.dudika_id');
            }

            $remaining = max(0, $totalDudika - $visited);

            // ── 5. HISTORY MONITORING ────────────────────────────────────────
            //    FIX: whereIn pkl_placement_id + eager load pklPlacement.dudika
            $year  = Carbon::parse($this->filterMonth)->format('Y');
            $month = Carbon::parse($this->filterMonth)->format('m');

            $historyData = Monitoring::with('pklPlacement.dudika')
                ->whereIn('pkl_placement_id', $placementIds)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->orderBy('date', 'desc')
                ->get()
                ->map(function ($h) use ($teacher) {

                    $dudikaId = $h->pklPlacement->dudika_id ?? null;

                    // Jumlah siswa aktif di instansi ini yang dibimbing guru ini
                    $studentsCovered = PklPlacement::where('teacher_id', $teacher->id)
                        ->where('dudika_id', $dudikaId)
                        ->where('status', 'Aktif')
                        ->count();

                    // Jumlah foto (format JSON array)
                    $photosCount = 0;
                    if (!empty($h->photo_path)) {
                        $decoded     = json_decode($h->photo_path, true);
                        $photosCount = is_array($decoded) ? count($decoded) : 1;
                    }

                    return [
                        'id'               => $h->id,
                        'date_num'         => Carbon::parse($h->date)->format('d'),
                        'date_month'       => Carbon::parse($h->date)->isoFormat('MMM'),
                        'date_full'        => Carbon::parse($h->date)->isoFormat('D MMMM YYYY'),
                        'dudika_name'      => $h->pklPlacement->dudika->name ?? 'Instansi Tidak Diketahui',
                        'students_covered' => $studentsCovered,
                        'photos_count'     => $photosCount,
                        'notes'            => $h->notes ?? 'Tidak ada catatan.',
                    ];
                });
        }

        return view('livewire.pembimbing.lapor', [
            'isActiveWindow'  => $isActiveWindow,
            'scheduleName'    => $scheduleName,
            'scheduleDateStr' => $scheduleDateStr,
            'visited'         => $visited,
            'remaining'       => $remaining,
            'history'         => $historyData,
        ]);
    }
}
