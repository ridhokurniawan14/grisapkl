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
    public $placement;
    public $search = '';
    public $selectedMonth = '';
    public $selectedStatus = ''; // Filter Absen, Sakit, Izin, Libur, Revisi

    public function mount()
    {
        $this->placement = PklPlacement::whereHas('student', function ($q) {
            $q->where('user_id', Auth::id());
        })->where('status', 'Aktif')->first();
    }

    public function render()
    {
        $journals = collect();
        $totalJurnal = 0;
        $totalRevisi = 0;
        $months = [];

        if ($this->placement) {
            $baseQuery = Journal::where('pkl_placement_id', $this->placement->id);

            // Statistik
            $totalJurnal = (clone $baseQuery)->count();
            // Asumsi is_valid = false artinya Perlu Revisi
            $totalRevisi = (clone $baseQuery)->where('is_valid', false)->count();

            // GENERATE BULAN BERDASARKAN RENTANG PKL
            if ($this->placement->start_date && $this->placement->end_date) {
                $start = Carbon::parse($this->placement->start_date)->startOfMonth();
                $end = Carbon::parse($this->placement->end_date)->startOfMonth();

                while ($start->lte($end)) {
                    $months[$start->format('Y-m')] = $start->isoFormat('MMMM YYYY');
                    $start->addMonth();
                }
            }

            // HANYA AMBIL DATA JIKA FILTER BULAN ATAU STATUS DIPILIH
            if (!empty($this->selectedMonth) || !empty($this->selectedStatus) || !empty($this->search)) {
                $query = clone $baseQuery;

                // Filter Bulan (Format Y-m)
                if (!empty($this->selectedMonth)) {
                    $year = substr($this->selectedMonth, 0, 4);
                    $month = substr($this->selectedMonth, 5, 2);
                    $query->whereYear('date', $year)->whereMonth('date', $month);
                }

                // Filter Status
                if (!empty($this->selectedStatus)) {
                    if ($this->selectedStatus === 'Revisi') {
                        $query->where('is_valid', false);
                    } else {
                        $query->where('attend_status', $this->selectedStatus);
                    }
                }

                // Filter Pencarian
                if (!empty($this->search)) {
                    $query->where('activity', 'like', '%' . $this->search . '%');
                }

                $journals = $query->orderBy('date', 'desc')->orderBy('time', 'desc')->get()->map(function ($j) {
                    $j->is_editable = Carbon::parse($j->date)->diffInDays(now()) <= 30; // Maksimal edit 30 hari
                    $j->attendance_photo_url = $j->attendance_photo_path ? asset('storage/' . $j->attendance_photo_path) : null;
                    $j->activity_photo_url = $j->photo_path ? asset('storage/' . $j->photo_path) : null;
                    return $j;
                });
            }
        }

        return view('livewire.student.jurnal', [
            'journals' => $journals,
            'totalJurnal' => $totalJurnal,
            'totalRevisi' => $totalRevisi,
            'months' => $months
        ]);
    }
}
