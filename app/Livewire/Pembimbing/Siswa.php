<?php

namespace App\Livewire\Pembimbing;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Daftar Siswa - GrisaPKL')]
class Siswa extends Component
{
    public $search = '';
    public $filterDudika = 'Semua Instansi';

    public function render()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return view('livewire.pembimbing.siswa', [
                'siswaList'  => collect(),
                'dudikaList' => [],
                'totalSiswa' => 0,
            ]);
        }

        $studentsQuery = Student::with([
            'user',
            'pklPlacements' => function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)
                    ->with(['dudika', 'journals']); // journals via placement
            },
        ])
            ->whereHas('pklPlacements', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->get();

        $allSiswa = $studentsQuery->map(function ($student) {

            // Ambil placement aktif milik teacher ini (sudah difilter di eager load)
            $placement = $student->pklPlacements->first();

            $startDate = $placement?->start_date
                ? Carbon::parse($placement->start_date)
                : Carbon::now()->subMonths(1);

            $endDate = $placement?->end_date
                ? Carbon::parse($placement->end_date)
                : Carbon::now()->addMonths(2);

            $totalExpectedLogs = max($startDate->diffInWeekdays($endDate), 1);

            // Jurnal sekarang dari placement, bukan dari student langsung
            $journals = $placement?->journals ?? collect();
            $submittedLogs = $journals->count();
            $hadirCount = $journals->where('attend_status', 'Hadir')->count();
            $attendancePercent = $submittedLogs > 0
                ? round(($hadirCount / $submittedLogs) * 100)
                : 0;

            $avatarPath = null;
            if ($student->user?->avatar_path) {
                $avatarPath = asset('storage/' . $student->user->avatar_path);
            }

            return [
                'id'                 => $student->id,
                'name'               => $student->user?->name ?? 'Siswa',
                'dudika_name'        => $placement?->dudika?->name ?? 'Belum Ada Instansi',
                'pkl_field'          => $placement?->pkl_field ?? null,
                'avatar'             => $avatarPath,
                'attendance_percent' => $attendancePercent,
                'log_count'          => $submittedLogs,
                'log_total'          => $totalExpectedLogs,
                'phone' => $this->formatPhone($student->phone),
            ];
        });

        $dudikaList = $allSiswa->pluck('dudika_name')->filter()->unique()->values()->toArray();

        $filteredSiswa = $allSiswa->filter(function ($item) {
            $matchSearch = empty($this->search)
                || stripos($item['name'], $this->search) !== false;
            $matchDudika = $this->filterDudika === 'Semua Instansi'
                || $item['dudika_name'] === $this->filterDudika;
            return $matchSearch && $matchDudika;
        });

        return view('livewire.pembimbing.siswa', [
            'siswaList'  => $filteredSiswa,
            'dudikaList' => $dudikaList,
            'totalSiswa' => $allSiswa->count(),
        ]);
    }
    private function formatPhone(?string $phone): ?string
    {
        if (empty($phone)) return null;

        $phone = preg_replace('/\D/', '', $phone); // hapus karakter non-angka

        // Ganti awalan 0 → 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Pastikan minimal panjang wajar (10 digit setelah 62)
        if (strlen($phone) < 10) return null;

        return $phone;
    }
}
