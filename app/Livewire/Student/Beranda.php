<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Student;
use App\Models\PklPlacement;
use App\Models\Journal;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Beranda - GrisaPKL')]
class Beranda extends Component
{
    public function render()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        $placement = PklPlacement::with('dudika')
            ->where('student_id', $student?->id)
            ->where('status', 'Aktif')
            ->first();

        // 1. Sapaan Berdasarkan Jam Lokal (WIB / Asia/Jakarta)
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

        // 2. Cek Kelengkapan Data
        $isBiodataComplete = $student && $student->nisn && $student->address;
        $isParentComplete = $student &&
            !empty($student->father_name) &&
            !empty($student->father_job) &&
            !empty($student->mother_name) &&
            !empty($student->mother_job) &&
            !empty($student->parent_phone) &&
            (!empty($student->parent_address) || !empty($student->address));
        $isDudikaComplete = $placement && !empty($placement->pkl_field);

        // 3. Cek Jurnal Revisi
        $revisiCount = 0;
        if ($placement) {
            $revisiCount = Journal::where('pkl_placement_id', $placement->id)
                ->where('is_valid', false)
                ->count();
        }
        $isJurnalRevisiClean = $revisiCount === 0;

        // 4. Rekap Absensi
        $recap = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0];
        if ($placement) {
            $journalsThisMonth = Journal::where('pkl_placement_id', $placement->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->get();

            $recap['Hadir'] = $journalsThisMonth->where('attend_status', 'Hadir')->where('is_valid', true)->count();
            $recap['Izin']  = $journalsThisMonth->where('attend_status', 'Izin')->count();
            $recap['Sakit'] = $journalsThisMonth->where('attend_status', 'Sakit')->count();

            $startOfMonth = now()->startOfMonth();
            $today = now();

            $pklStart = Carbon::parse($placement->start_date);
            if ($pklStart->isCurrentMonth() && $pklStart->gt($startOfMonth)) {
                $startOfMonth = $pklStart;
            }

            $workingDays = 0;
            if ($startOfMonth->lte($today)) {
                $workingDays = $startOfMonth->diffInDaysFiltered(function (Carbon $date) {
                    return !$date->isWeekend();
                }, $today) + 1;
            }

            $loggedDays = $journalsThisMonth->whereIn('attend_status', ['Hadir', 'Izin', 'Sakit', 'Libur'])->count();
            $recap['Alpha'] = max(0, $workingDays - $loggedDays);
        }

        // 5. Tarik Data Pengumuman (Aktif, Khusus Siswa & Umum)
        $announcements = Announcement::where('is_active', 1)
            ->whereIn('target_audience', ['Umum', 'Siswa'])
            ->latest()
            ->get();

        return view('livewire.student.beranda', [
            'user' => $user,
            'student' => $student,
            'placement' => $placement,
            'greeting' => $greeting,
            'isBiodataComplete' => $isBiodataComplete,
            'isParentComplete' => $isParentComplete,
            'isDudikaComplete' => $isDudikaComplete,
            'isJurnalRevisiClean' => $isJurnalRevisiClean,
            'revisiCount' => $revisiCount,
            'recap' => $recap,
            'announcements' => $announcements, // Kirim ke View
        ]);
    }
}
