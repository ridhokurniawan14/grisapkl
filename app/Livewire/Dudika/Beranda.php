<?php

namespace App\Livewire\Dudika;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Dudika;
use App\Models\PklPlacement;
use App\Models\Announcement; // Tambahkan Model Pengumuman
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Beranda DUDIKA - GrisaPKL')]
class Beranda extends Component
{
    public $search = '';

    public function render()
    {
        // 1. Sapaan Berdasarkan Jam Lokal (WIB)
        $hour = Carbon::now('Asia/Jakarta')->format('H');
        if ($hour >= 4 && $hour < 11) {
            $greeting = 'Selamat Pagi,';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat Siang,';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat Sore,';
        } else {
            $greeting = 'Selamat Malam,';
        }

        $user = Auth::user();
        $dudika = Dudika::where('user_id', $user->id)->first();

        // 2. Data Info DUDIKA
        $dudikaName = $dudika ? ($dudika->supervisor_name ?? $dudika->name) : $user->name;

        // REVISI 1: Logika Kelengkapan (NIP/NIK Opsional)
        $isComplete = false;
        if ($dudika) {
            $isComplete = !empty($dudika->name) &&
                !empty($dudika->address) &&
                !empty($dudika->head_name) &&
                !empty($dudika->supervisor_name) &&
                !empty($dudika->supervisor_phone);
        }

        $students = collect();

        // 3. Tarik Data Siswa Magang yang Aktif
        if ($dudika) {
            $placements = PklPlacement::with(['student.user', 'student.studentClass.major', 'journals'])
                ->where('dudika_id', $dudika->id)
                ->where('status', 'Aktif')
                ->get();

            $students = $placements->map(function ($p) {
                $student = $p->student;

                // A. Logika Avatar Super Kebal
                $avatarPath = null;
                if (!empty($student->user->avatar)) {
                    $avatarPath = asset('storage/' . $student->user->avatar);
                } elseif (!empty($student->user->avatar_path)) {
                    $avatarPath = asset('storage/' . $student->user->avatar_path);
                } elseif (!empty($student->avatar)) {
                    $avatarPath = asset('storage/' . $student->avatar);
                } elseif (!empty($student->avatar_path)) {
                    $avatarPath = asset('storage/' . $student->avatar_path);
                }

                // B. Format Nomor HP (08 -> 628)
                $phone = $student->phone ?? ($student->user->phone ?? null);
                if ($phone) {
                    $phone = preg_replace('/\D/', '', $phone);
                    if (str_starts_with($phone, '0')) {
                        $phone = '62' . substr($phone, 1);
                    }
                }

                // C. Hitung Rekap Kehadiran
                $journals = $p->journals;
                $h = $journals->where('attend_status', 'Hadir')->where('is_valid', true)->count();
                $i = $journals->where('attend_status', 'Izin')->count();
                $s = $journals->where('attend_status', 'Sakit')->count();
                $l = $journals->where('attend_status', 'Libur')->count();

                // D. Logika Hitung Alpha Akurat (Hanya Hari Kerja)
                $startDate = $p->start_date ? Carbon::parse($p->start_date)->startOfDay() : Carbon::now()->startOfDay();
                $endDate   = $p->end_date ? Carbon::parse($p->end_date)->endOfDay() : Carbon::now()->addMonths(6)->endOfDay();
                $today     = Carbon::now()->endOfDay();

                $limitDate = $today->lessThan($endDate) ? $today : $endDate;

                $workingDays = 0;
                if ($startDate->lessThanOrEqualTo($limitDate)) {
                    $period = \Carbon\CarbonPeriod::create($startDate, $limitDate);
                    foreach ($period as $date) {
                        if ($date->isWeekday()) {
                            $workingDays++;
                        }
                    }
                }

                $loggedDays = $journals->whereBetween('date', [$startDate->format('Y-m-d'), $limitDate->format('Y-m-d')])
                    ->filter(function ($j) {
                        return Carbon::parse($j->date)->isWeekday();
                    })
                    ->pluck('date')
                    ->unique()
                    ->count();

                $alpha = max(0, $workingDays - $loggedDays);

                return [
                    'id'    => $student->id,
                    'name'  => $student->user->name ?? $student->name,
                    'field' => $p->pkl_field ?? ($student->studentClass->major->name ?? 'Jurusan Umum'),
                    'avatar' => $avatarPath,
                    'phone' => $phone,
                    'recap' => ['H' => $h, 'I' => $i, 'S' => $s, 'L' => $l, 'A' => $alpha],
                ];
            });
        }

        // 4. Filter Pencarian
        $filteredStudents = $students->filter(function ($siswa) {
            $matchSearch = empty($this->search)
                || stripos($siswa['name'], $this->search) !== false
                || stripos($siswa['field'], $this->search) !== false;
            return $matchSearch;
        });

        // REVISI 2: Tarik Data Pengumuman untuk DUDIKA
        $announcements = Announcement::where('is_active', 1)
            ->whereIn('target_audience', ['Umum', 'Dudika']) // Pastikan enum "Dudika" ada di target_audience table announcements
            ->latest()
            ->get();

        return view('livewire.dudika.beranda', [
            'greeting'      => $greeting,
            'dudikaName'    => $dudikaName,
            'isComplete'    => $isComplete,
            'students'      => $filteredStudents,
            'announcements' => $announcements,
        ]);
    }
}
