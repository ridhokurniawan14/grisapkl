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
    public $selectedDudikaId  = '';
    public $monitoringNotes   = '';   // maps to kolom `activity` di DB
    public $monitoringPhoto   = null;

    public function mount(): void
    {
        $this->filterMonth = Carbon::now()->format('Y-m');
    }

    // ── ACTION: Ganti bulan filter ─────────────────────────────────────────
    public function setFilterMonth(string $month): void
    {
        $this->filterMonth = $month;
    }

    // ── ACTION: Buka form & set default activity dari nama jadwal aktif ────
    public function openReportForm(): void
    {
        $activeSchedule        = MonitoringSchedule::where('is_active', 1)->latest()->first();
        // Default catatan = nama jadwal (kolom `name` di monitoring_schedules)
        $this->monitoringNotes  = $activeSchedule ? $activeSchedule->name : '';
        $this->selectedDudikaId = '';
        $this->monitoringPhoto  = null;
        $this->resetErrorBag();
        $this->dispatch('open-report-form');
    }

    // ── ACTION: Simpan laporan monitoring ─────────────────────────────────
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

        $placement = PklPlacement::where('teacher_id', $teacher->id)
            ->where('dudika_id', $this->selectedDudikaId)
            ->where('status', 'Aktif')
            ->first();

        if (!$placement) {
            $this->addError('selectedDudikaId', 'DUDIKA yang dipilih tidak valid.');
            return;
        }

        // photo_path adalah varchar(255) — simpan path tunggal, bukan JSON
        $photoPath = null;
        if ($this->monitoringPhoto) {
            $photoPath = $this->monitoringPhoto->store('monitorings', 'public');
        }

        Monitoring::create([
            'pkl_placement_id'       => $placement->id,
            'monitoring_schedule_id' => $activeSchedule->id,
            'date'                   => Carbon::now()->toDateString(),
            'time'                   => Carbon::now()->format('H:i:s'),
            'activity'               => $this->monitoringNotes,  // kolom `activity`, bukan `notes`
            'photo_path'             => $photoPath,
        ]);

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
            // Semua placement aktif milik guru ini
            $placements   = PklPlacement::where('teacher_id', $teacher->id)
                ->where('status', 'Aktif')
                ->get(['id', 'dudika_id']);

            $placementIds = $placements->pluck('id');
            $totalDudika  = $placements->pluck('dudika_id')->filter()->unique()->count();

            // ── DUDIKA untuk dropdown form ──────────────────────────────────
            $dudikasForTeacher = PklPlacement::where('teacher_id', $teacher->id)
                ->where('status', 'Aktif')
                ->with('dudika:id,name')
                ->get()
                ->map(fn($p) => $p->dudika)
                ->filter()
                ->unique('id')
                ->values();

            // ── JADWAL AKTIF ────────────────────────────────────────────────
            if ($activeSchedule) {
                $activeScheduleId = $activeSchedule->id;
                $scheduleName     = strtoupper($activeSchedule->name);
                $start = Carbon::parse($activeSchedule->start_date);
                $end   = Carbon::parse($activeSchedule->end_date);

                $scheduleDateStr = $start->isoFormat('D MMM YYYY') . ' - ' . $end->isoFormat('D MMM YYYY');
                $isActiveWindow  = Carbon::now()->betweenIncluded($start->startOfDay(), $end->endOfDay());

                // Hitung DUDIKA unik yang sudah divisit di jadwal aktif ini
                $visited = Monitoring::where('monitoring_schedule_id', $activeScheduleId)
                    ->whereIn('pkl_placement_id', $placementIds)
                    ->join('pkl_placements', 'monitorings.pkl_placement_id', '=', 'pkl_placements.id')
                    ->distinct('pkl_placements.dudika_id')
                    ->count('pkl_placements.dudika_id');
            }

            $remaining = max(0, $totalDudika - $visited);

            if ($placementIds->isNotEmpty()) {
                // ── BULAN YANG ADA DATANYA saja (untuk pills filter) ────────
                $availableMonths = Monitoring::whereIn('pkl_placement_id', $placementIds)
                    ->selectRaw('DATE_FORMAT(date, "%Y-%m") as ym')
                    ->distinct()
                    ->orderBy('ym', 'desc')
                    ->pluck('ym')
                    ->map(fn($ym) => [
                        'value' => $ym,
                        'label' => Carbon::parse($ym . '-01')->isoFormat('MMM YYYY'),
                    ]);

                // Auto-select ke bulan terbaru yang ada data
                // jika filterMonth sekarang tidak ada di daftar
                if (
                    $availableMonths->isNotEmpty() &&
                    !$availableMonths->contains('value', $this->filterMonth)
                ) {
                    $this->filterMonth = $availableMonths->first()['value'];
                }

                // ── HISTORY MONITORING ──────────────────────────────────────
                $year  = Carbon::parse($this->filterMonth . '-01')->format('Y');
                $month = Carbon::parse($this->filterMonth . '-01')->format('m');

                $historyData = Monitoring::with('pklPlacement.dudika')
                    ->whereIn('pkl_placement_id', $placementIds)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->orderBy('date', 'desc')
                    ->get()
                    ->map(function ($h) use ($teacher) {
                        $dudikaId = $h->pklPlacement->dudika_id ?? null;

                        $studentsCovered = PklPlacement::where('teacher_id', $teacher->id)
                            ->where('dudika_id', $dudikaId)
                            ->where('status', 'Aktif')
                            ->count();

                        // photo_path adalah varchar tunggal, bukan JSON array
                        $hasPhoto = !empty($h->photo_path);

                        return [
                            'id'               => $h->id,
                            'date_num'         => Carbon::parse($h->date)->format('d'),
                            'date_month'       => Carbon::parse($h->date)->isoFormat('MMM'),
                            'date_full'        => Carbon::parse($h->date)->isoFormat('D MMMM YYYY'),
                            'time'             => $h->time ? Carbon::parse($h->time)->format('H:i') : '-',
                            'dudika_name'      => $h->pklPlacement->dudika->name ?? 'Instansi Tidak Diketahui',
                            'students_covered' => $studentsCovered,
                            'photos_count'     => $hasPhoto ? 1 : 0,
                            'photo_url'        => $hasPhoto ? asset('storage/' . $h->photo_path) : null,
                            'notes'            => $h->activity ?? 'Tidak ada catatan.',
                        ];
                    });
            }
        }

        return view('livewire.pembimbing.lapor', [
            'isActiveWindow'    => $isActiveWindow,
            'scheduleName'      => $scheduleName,
            'scheduleDateStr'   => $scheduleDateStr,
            'visited'           => $visited,
            'remaining'         => $remaining,
            'history'           => $historyData,
            'availableMonths'   => $availableMonths,
            'dudikasForTeacher' => $dudikasForTeacher,
        ]);
    }
}
