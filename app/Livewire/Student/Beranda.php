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

        // 4. Rekap Absensi Bulan Ini (dihitung dari start_date PKL, bukan awal bulan)
        $recap = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0];
        if ($placement) {
            $today        = Carbon::now('Asia/Jakarta')->endOfDay();
            $startOfMonth = Carbon::now('Asia/Jakarta')->startOfMonth()->startOfDay();
            $endOfMonth   = Carbon::now('Asia/Jakarta')->endOfMonth()->endOfDay();

            // Batas awal range: mana yang lebih besar antara awal bulan vs start_date PKL
            $pklStart   = Carbon::parse($placement->start_date)->startOfDay();
            $rangeStart = $pklStart->gt($startOfMonth) ? $pklStart->copy() : $startOfMonth->copy();

            // Batas akhir range: mana yang lebih kecil antara hari ini vs akhir bulan
            $rangeEnd = $today->lt($endOfMonth) ? $today->copy() : $endOfMonth->copy();

            // Kalau PKL belum mulai bulan ini, biarkan semua rekap 0
            if ($rangeStart->lte($rangeEnd)) {
                // Ambil jurnal hanya dalam range yang valid (dari start_date PKL s.d. hari ini)
                $journalsInRange = Journal::where('pkl_placement_id', $placement->id)
                    ->whereBetween('date', [
                        $rangeStart->toDateString(),
                        $rangeEnd->toDateString(),
                    ])
                    ->get();

                $recap['Hadir'] = $journalsInRange->where('attend_status', 'Hadir')->where('is_valid', true)->count();
                $recap['Izin']  = $journalsInRange->where('attend_status', 'Izin')->count();
                $recap['Sakit'] = $journalsInRange->where('attend_status', 'Sakit')->count();

                // Hitung hari kerja (Senin–Jumat) dalam range menggunakan CarbonPeriod
                $workingDays = 0;
                $period = \Carbon\CarbonPeriod::create($rangeStart, $rangeEnd);
                foreach ($period as $date) {
                    if ($date->isWeekday()) $workingDays++;
                }

                // Logged = tanggal unik yang sudah ada jurnalnya (semua status kecuali Alpha)
                $loggedDays = $journalsInRange
                    ->whereIn('attend_status', ['Hadir', 'Izin', 'Sakit', 'Libur'])
                    ->pluck('date')
                    ->unique()
                    ->count();

                $recap['Alpha'] = max(0, $workingDays - $loggedDays);
            }
        }

        // 5. Tarik Data Pengumuman (Aktif, Khusus Siswa & Umum)
        $announcements = Announcement::where('is_active', 1)
            ->whereIn('target_audience', ['Umum', 'Siswa'])
            ->latest()
            ->get();

        return view('livewire.student.beranda', [
            'user'               => $user,
            'student'            => $student,
            'placement'          => $placement,
            'greeting'           => $greeting,
            'isBiodataComplete'  => $isBiodataComplete,
            'isParentComplete'   => $isParentComplete,
            'isDudikaComplete'   => $isDudikaComplete,
            'isJurnalRevisiClean' => $isJurnalRevisiClean,
            'revisiCount'        => $revisiCount,
            'recap'              => $recap,
            'announcements'      => $announcements,
        ]);
    }
}
