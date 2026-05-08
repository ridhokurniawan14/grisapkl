<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Journal;
use App\Models\PklPlacement;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Jurnal Siswa - GrisaPKL')]
class Jurnal extends Component
{
    // ✅ Simpan ID saja, bukan full model — hindari hydration failure
    public ?int $placementId = null;
    public string $search = '';
    public string $selectedMonth = '';
    public string $selectedStatus = '';

    public function mount(): void
    {
        $placement = PklPlacement::whereHas('student', function ($q) {
            $q->where('user_id', Auth::id());
        })->where('status', 'Aktif')->first();

        $this->placementId = $placement?->id;
    }

    public function render()
    {
        $journals = collect();
        $totalJurnal = 0;
        $totalRevisi = 0;
        $months = [];

        if ($this->placementId) {

            $placement = PklPlacement::find($this->placementId);

            if ($placement) {

                $baseQuery = Journal::where('pkl_placement_id', $placement->id);

                $totalJurnal = (clone $baseQuery)->count();

                $totalRevisi = (clone $baseQuery)
                    ->where('is_valid', 0)
                    ->count();

                // Generate bulan
                if ($placement->start_date && $placement->end_date) {

                    $start = Carbon::parse($placement->start_date)->startOfMonth();
                    $end = Carbon::parse($placement->end_date)->startOfMonth();

                    while ($start->lte($end)) {

                        $months[$start->format('Y-m')] = $start->isoFormat('MMMM YYYY');

                        $start->addMonth();
                    }
                }

                // QUERY FILTER
                $query = Journal::where('pkl_placement_id', $placement->id);

                // FILTER BULAN
                if (filled($this->selectedMonth) && str_contains($this->selectedMonth, '-')) {

                    [$year, $month] = explode('-', $this->selectedMonth);

                    $query->whereYear('date', $year)
                        ->whereMonth('date', $month);
                }

                // FILTER STATUS
                if (filled($this->selectedStatus)) {

                    if ($this->selectedStatus === 'Revisi') {

                        $query->where('is_valid', 0);
                    } else {

                        $query->where('attend_status', $this->selectedStatus);
                    }
                }

                // SEARCH
                if (filled($this->search)) {

                    $query->whereNotNull('activity')
                        ->where('activity', 'like', '%' . $this->search . '%');
                }

                $journals = $query
                    ->orderByDesc('date')
                    ->orderByDesc('time')
                    ->get()
                    ->map(function ($j) {

                        $j->formatted_date = Carbon::parse($j->date)->isoFormat('D MMM YYYY');

                        $j->formatted_time = Carbon::parse($j->time)->format('H:i');

                        $j->is_editable = Carbon::parse($j->date)->diffInDays(now()) <= 30;

                        $j->attendance_photo_url = $j->attendance_photo_path
                            ? asset('storage/' . $j->attendance_photo_path)
                            : null;

                        $j->activity_photo_url = $j->photo_path
                            ? asset('storage/' . $j->photo_path)
                            : null;

                        return $j;
                    });
            }
        }

        return view('livewire.student.jurnal', [
            'journals' => $journals,
            'totalJurnal' => $totalJurnal,
            'totalRevisi' => $totalRevisi,
            'months' => $months,
        ]);
    }
}
