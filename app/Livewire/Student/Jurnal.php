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
    public ?int $placementId = null;

    // Variabel Filter
    public string $selectedMonth = '';
    public string $selectedStatus = '';
    public $filterStartDate;
    public $filterEndDate;

    // Mode khusus: menampilkan hanya jurnal yang fotonya belum lengkap
    public bool $showOnlyIncomplete = false;

    public function mount(): void
    {
        $placement = PklPlacement::whereHas('student', function ($q) {
            $q->where('user_id', Auth::id());
        })->where('status', 'Aktif')->first();

        $this->placementId = $placement?->id;
    }

    /**
     * Dipanggil saat siswa klik tombol "Perbaiki" di modal notifikasi.
     * Mereset semua filter lain, lalu menyalakan mode "tampilkan yang belum lengkap saja".
     */
    public function tampilkanBelumLengkap(): void
    {
        $this->reset(['selectedMonth', 'selectedStatus', 'filterStartDate', 'filterEndDate']);
        $this->showOnlyIncomplete = true;
    }

    /**
     * Reset mode "belum lengkap" (misalnya saat siswa klik tombol filter manual lagi).
     */
    public function resetModeLengkap(): void
    {
        $this->showOnlyIncomplete = false;
    }

    /**
     * Query dasar untuk mendeteksi jurnal yang fotonya belum lengkap
     * berdasarkan aturan: Hadir wajib 2 foto, Sakit/Izin wajib 1 foto, Libur tidak wajib foto.
     */
    private function buildIncompleteQuery(int $placementId)
    {
        return Journal::where('pkl_placement_id', $placementId)
            ->where(function ($q) {
                $q->where(function ($qq) {
                    // Hadir: wajib ada foto selfie absen DAN foto kegiatan
                    $qq->where('attend_status', 'Hadir')
                        ->where(function ($q2) {
                            $q2->whereNull('attendance_photo_path')
                                ->orWhere('attendance_photo_path', '')
                                ->orWhereNull('photo_path')
                                ->orWhere('photo_path', '');
                        });
                })->orWhere(function ($qq) {
                    // Sakit / Izin: wajib ada foto kegiatan saja
                    $qq->whereIn('attend_status', ['Sakit', 'Izin'])
                        ->where(function ($q2) {
                            $q2->whereNull('photo_path')->orWhere('photo_path', '');
                        });
                });
            });
    }

    public function render()
    {
        $journals = collect();
        $totalJurnal = 0;
        $totalRevisi = 0;
        $months = [];

        // Data notifikasi "foto belum lengkap"
        $incompleteCount = 0;
        $incompleteDates = [];

        if ($this->placementId) {

            $placement = PklPlacement::find($this->placementId);

            if ($placement) {

                $baseQuery = Journal::where('pkl_placement_id', $placement->id);

                $totalJurnal = (clone $baseQuery)->count();

                $totalRevisi = (clone $baseQuery)
                    ->where('is_valid', 0)
                    ->count();

                // Cek jurnal yang fotonya belum lengkap, dari SELURUH riwayat jurnal
                // (tidak terpengaruh filter bulan/status/tanggal yang sedang aktif)
                $incompleteBase = $this->buildIncompleteQuery($placement->id);

                $incompleteCount = (clone $incompleteBase)->count();

                $incompleteDates = (clone $incompleteBase)
                    ->orderByDesc('date')
                    ->limit(6)
                    ->get(['date', 'attend_status'])
                    ->map(fn($j) => [
                        'date' => Carbon::parse($j->date)->isoFormat('D MMM YYYY'),
                        'status' => $j->attend_status,
                    ])
                    ->toArray();

                // Generate bulan
                if ($placement->start_date && $placement->end_date) {
                    $start = Carbon::parse($placement->start_date)->startOfMonth();
                    $end = Carbon::parse($placement->end_date)->startOfMonth();

                    while ($start->lte($end)) {
                        $months[$start->format('Y-m')] = $start->isoFormat('MMMM YYYY');
                        $start->addMonth();
                    }
                }

                if ($this->showOnlyIncomplete) {
                    // MODE "PERBAIKI": tampilkan semua jurnal yang fotonya belum lengkap,
                    // lintas bulan/tanggal, tanpa terpengaruh filter manual lainnya.
                    $query = $this->buildIncompleteQuery($placement->id);
                } else {
                    // QUERY FILTER UTAMA
                    $query = Journal::where('pkl_placement_id', $placement->id);

                    // 1. FILTER BULAN
                    if (filled($this->selectedMonth) && str_contains($this->selectedMonth, '-')) {
                        [$year, $month] = explode('-', $this->selectedMonth);
                        $query->whereYear('date', $year)
                            ->whereMonth('date', $month);
                    }

                    // 2. FILTER STATUS
                    if (filled($this->selectedStatus)) {
                        if ($this->selectedStatus === 'Revisi') {
                            $query->where('is_valid', 0);
                        } else {
                            $query->where('attend_status', $this->selectedStatus);
                        }
                    }

                    // 3. FILTER RENTANG TANGGAL (PENGGANTI SEARCH)
                    if (filled($this->filterStartDate) && filled($this->filterEndDate)) {
                        $query->whereBetween('date', [$this->filterStartDate, $this->filterEndDate]);
                    } elseif (filled($this->filterStartDate)) {
                        $query->whereDate('date', '>=', $this->filterStartDate);
                    } elseif (filled($this->filterEndDate)) {
                        $query->whereDate('date', '<=', $this->filterEndDate);
                    }
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
            'incompleteCount' => $incompleteCount,
            'incompleteDates' => $incompleteDates,
        ]);
    }
}
