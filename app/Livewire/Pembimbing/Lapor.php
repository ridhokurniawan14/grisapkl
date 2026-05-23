<?php

namespace App\Livewire\Pembimbing;

use Livewire\Component;
use Livewire\WithFileUploads;
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
    use WithFileUploads;

    public $filterMonth;

    // ── State form modal ───────────────────────────────────────────────────
    public $selectedDudikaId = '';
    public $monitoringNotes  = '';
    public $monitoringPhoto  = null;

    public function mount(): void
    {
        $this->filterMonth = Carbon::now()->format('Y-m');
    }

    public function setFilterMonth(string $month): void
    {
        $this->filterMonth = $month;
    }

    // ── ACTION: Buka form (DENGAN PROTEKSI TAMBAHAN) ─────
    public function openReportForm(): void
    {
        $user           = Auth::user();
        $teacher        = Teacher::where('user_id', $user->id)->first();
        $activeSchedule = MonitoringSchedule::where('is_active', 1)->latest()->first();

        if (!$teacher || !$activeSchedule) return;

        // Validasi Ekstra: Pastikan masih ada sisa instansi yang belum dikunjungi
        $placements = PklPlacement::where('teacher_id', $teacher->id)
            ->where('status', 'Aktif')
            ->get(['id', 'dudika_id']);

        $totalDudika = $placements->pluck('dudika_id')->filter()->unique()->count();
        $visited = Monitoring::where('monitoring_schedule_id', $activeSchedule->id)
            ->whereIn('pkl_placement_id', $placements->pluck('id'))
            ->join('pkl_placements', 'monitorings.pkl_placement_id', '=', 'pkl_placements.id')
            ->distinct('pkl_placements.dudika_id')
            ->count('pkl_placements.dudika_id');

        // Jika semua instansi sudah dikunjungi, tolak buka form
        if (($totalDudika - $visited) <= 0) {
            return;
        }

        $this->monitoringNotes  = $activeSchedule->name;
        $this->selectedDudikaId = '';
        $this->monitoringPhoto  = null;
        $this->resetErrorBag();
        $this->dispatch('open-report-form');
    }

    public function submitMonitoring(): void
    {
        $this->validate([
            'selectedDudikaId' => 'required',
            'monitoringNotes'  => 'required|string|max:1000',
            'monitoringPhoto'  => 'nullable|image|max:5120',
        ], [
            'selectedDudikaId.required' => 'Pilih DUDIKA terlebih dahulu.',
            'monitoringNotes.required'  => 'Catatan tidak boleh kosong.',
            'monitoringPhoto.image'     => 'File harus berupa gambar.',
            'monitoringPhoto.max'       => 'Ukuran foto maksimal 5MB.',
        ]);

        $user           = Auth::user();
        $teacher        = Teacher::where('user_id', $user->id)->first();
        $activeSchedule = MonitoringSchedule::where('is_active', 1)->latest()->first();

        if (!$teacher || !$activeSchedule) {
            $this->addError('selectedDudikaId', 'Data jadwal atau guru tidak ditemukan.');
            return;
        }

        $placements = PklPlacement::where('teacher_id', $teacher->id)
            ->where('dudika_id', $this->selectedDudikaId)
            ->where('status', 'Aktif')
            ->get();

        if ($placements->isEmpty()) {
            $this->addError('selectedDudikaId', 'Tidak ada siswa aktif di DUDIKA yang dipilih.');
            return;
        }

        $photoPath = null;
        if ($this->monitoringPhoto) {
            $photoPath = $this->monitoringPhoto->store('monitorings', 'public');
        }

        $now = Carbon::now();

        foreach ($placements as $placement) {
            Monitoring::create([
                'pkl_placement_id'       => $placement->id,
                'monitoring_schedule_id' => $activeSchedule->id,
                'date'                   => $now->toDateString(),
                'time'                   => $now->format('H:i:s'),
                'activity'               => $this->monitoringNotes,
                'photo_path'             => $photoPath,
            ]);
        }

        $this->reset(['selectedDudikaId', 'monitoringNotes', 'monitoringPhoto']);
        $this->dispatch('close-report-form');
        $this->dispatch('monitoring-saved');
    }

    public function render()
    {
        $user    = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        $activeSchedule = MonitoringSchedule::where('is_active', 1)->latest()->first();

        $isActiveWindow    = false;
        $scheduleName      = 'TIDAK ADA JADWAL AKTIF';
        $scheduleDateStr   = '-';
        $activeScheduleId  = null;
        $totalDudika       = 0;
        $visited           = 0;
        $remaining         = 0;
        $historyData       = collect();
        $availableMonths   = collect();
        $dudikasForTeacher = collect();

        if ($teacher) {
            $placements   = PklPlacement::where('teacher_id', $teacher->id)
                ->where('status', 'Aktif')
                ->get(['id', 'dudika_id']);

            $placementIds = $placements->pluck('id');
            $totalDudika  = $placements->pluck('dudika_id')->filter()->unique()->count();

            // Ambil semua dudika milik teacher ini
            $allDudikas = PklPlacement::where('teacher_id', $teacher->id)
                ->where('status', 'Aktif')
                ->with('dudika:id,name')
                ->get()
                ->map(fn($p) => $p->dudika)
                ->filter()
                ->unique('id')
                ->values();

            // Hanya tampilkan dudika yang BELUM dikunjungi di jadwal aktif
            if ($activeSchedule) {
                $visitedDudikaIds = Monitoring::where('monitoring_schedule_id', $activeSchedule->id)
                    ->whereIn('pkl_placement_id', $placementIds)
                    ->join('pkl_placements', 'monitorings.pkl_placement_id', '=', 'pkl_placements.id')
                    ->distinct()
                    ->pluck('pkl_placements.dudika_id')
                    ->filter()
                    ->values();

                $dudikasForTeacher = $allDudikas->reject(fn($d) => $visitedDudikaIds->contains($d->id))->values();
            } else {
                $dudikasForTeacher = $allDudikas;
            }

            if ($activeSchedule) {
                $activeScheduleId = $activeSchedule->id;
                $scheduleName     = strtoupper($activeSchedule->name);
                $start = Carbon::parse($activeSchedule->start_date);
                $end   = Carbon::parse($activeSchedule->end_date);

                $scheduleDateStr = $start->isoFormat('D MMM YYYY') . ' - ' . $end->isoFormat('D MMM YYYY');
                $isActiveWindow  = Carbon::now()->betweenIncluded($start->startOfDay(), $end->endOfDay());

                $visited = Monitoring::where('monitoring_schedule_id', $activeScheduleId)
                    ->whereIn('pkl_placement_id', $placementIds)
                    ->join('pkl_placements', 'monitorings.pkl_placement_id', '=', 'pkl_placements.id')
                    ->distinct('pkl_placements.dudika_id')
                    ->count('pkl_placements.dudika_id');
            }

            $remaining = max(0, $totalDudika - $visited);

            if ($placementIds->isNotEmpty()) {
                $availableMonths = Monitoring::whereIn('pkl_placement_id', $placementIds)
                    ->selectRaw('DATE_FORMAT(date, "%Y-%m") as ym')
                    ->distinct()
                    ->orderBy('ym', 'desc')
                    ->pluck('ym')
                    ->map(fn($ym) => [
                        'value' => $ym,
                        'label' => Carbon::parse($ym . '-01')->isoFormat('MMM YYYY'),
                    ]);

                if ($availableMonths->isNotEmpty() && !$availableMonths->contains('value', $this->filterMonth)) {
                    $this->filterMonth = $availableMonths->first()['value'];
                }

                $year  = Carbon::parse($this->filterMonth . '-01')->format('Y');
                $month = Carbon::parse($this->filterMonth . '-01')->format('m');

                $historyData = Monitoring::with('pklPlacement.dudika')
                    ->whereIn('pkl_placement_id', $placementIds)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->orderBy('date', 'desc')
                    ->orderBy('time', 'desc')
                    ->get()
                    ->groupBy(function ($h) {
                        $dudikaId = $h->pklPlacement->dudika_id ?? 0;
                        return $h->date . '_' . $dudikaId;
                    })
                    ->map(function ($group) {
                        $first    = $group->first();
                        $hasPhoto = $group->filter(fn($h) => !empty($h->photo_path))->isNotEmpty();
                        $photoRec = $group->first(fn($h) => !empty($h->photo_path));

                        return [
                            'id'               => $first->id,
                            'monitoring_ids'   => $group->pluck('id')->toArray(),
                            'date_num'         => Carbon::parse($first->date)->format('d'),
                            'date_month'       => Carbon::parse($first->date)->isoFormat('MMM'),
                            'date_full'        => Carbon::parse($first->date)->isoFormat('D MMMM YYYY'),
                            'time'             => $first->time
                                ? Carbon::parse($first->time)->format('H:i')
                                : '-',
                            'dudika_name'      => $first->pklPlacement->dudika->name
                                ?? 'Instansi Tidak Diketahui',
                            'students_covered' => $group->count(),
                            'photos_count'     => $hasPhoto ? 1 : 0,
                            'photo_url'        => $photoRec
                                ? asset('storage/' . $photoRec->photo_path)
                                : null,
                            'notes'            => $first->activity ?? 'Tidak ada catatan.',
                        ];
                    })
                    ->values();
            }
        }

        return view('livewire.pembimbing.lapor', [
            'isActiveWindow'    => $isActiveWindow,
            'scheduleName'      => $scheduleName,
            'scheduleDateStr'   => $scheduleDateStr,
            'visited'           => $visited,
            'remaining'         => $remaining,
            'totalDudika'       => $totalDudika, // PERBAIKAN: Lempar total Dudika ke View
            'history'           => $historyData,
            'availableMonths'   => $availableMonths,
            'dudikasForTeacher' => $dudikasForTeacher,
        ]);
    }
}
