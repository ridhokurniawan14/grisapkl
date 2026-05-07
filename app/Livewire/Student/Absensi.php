<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use App\Models\Journal;
use App\Models\PklPlacement;
use App\Models\SchoolProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Absensi - GrisaPKL')]
class Absensi extends Component
{
    use WithFileUploads;

    public $placement;
    public $hasAttendedToday = false;
    public $todayJournal;

    public $activity = '';
    public $activityPhoto;

    public function mount()
    {
        $this->placement = PklPlacement::whereHas('student', function ($q) {
            $q->where('user_id', Auth::id());
        })->where('status', 'Aktif')->first();

        if ($this->placement) {
            $this->checkAttendanceStatus();
        }
    }

    public function checkAttendanceStatus()
    {
        $this->todayJournal = Journal::where('pkl_placement_id', $this->placement->id)
            ->whereDate('date', today())
            ->first();

        $this->hasAttendedToday = $this->todayJournal ? true : false;
    }

    public function verifyLocation($lat, $lng)
    {
        $school = SchoolProfile::first();

        if ($school && $school->is_radius_attendance_enabled) {
            $placementLat = $this->placement->latitude;
            $placementLng = $this->placement->longitude;
            $maxRadius = $this->placement->radius ?? 50;

            if (!$placementLat || !$placementLng) return true;

            $distance = $this->calculateDistance($lat, $lng, $placementLat, $placementLng);
            if ($distance > $maxRadius) return false;
        }

        return true;
    }

    public function submitAttendance($photoBase64, $lat, $lng)
    {
        if (!$this->placement || $this->hasAttendedToday) return;
        if (!$this->verifyLocation($lat, $lng)) return;

        $imageParts = explode(";base64,", $photoBase64);
        $imageTypeAux = explode("image/", $imageParts[0]);
        $imageType = $imageTypeAux[1] ?? 'png';
        $imageBase64 = base64_decode($imageParts[1]);
        $fileName = 'attendance/' . Str::uuid() . '.' . $imageType;

        Storage::disk('public')->put($fileName, $imageBase64);

        Journal::create([
            'pkl_placement_id'      => $this->placement->id,
            'date'                  => now()->format('Y-m-d'),
            'time'                  => now()->format('H:i:s'),
            'attendance_photo_path' => $fileName,
            'attend_status'         => 'Hadir',
            'latitude'              => $lat,
            'longitude'             => $lng,
            'is_valid'              => true,
            'activity'              => '',
        ]);

        $this->checkAttendanceStatus();
    }

    public function markAttendance($status)
    {
        if (!$this->placement || $this->hasAttendedToday) return;

        $activityText = ($status === 'Libur') ? 'Libur / Tanggal Merah' : '';

        Journal::create([
            'pkl_placement_id' => $this->placement->id,
            'date'             => now()->format('Y-m-d'),
            'time'             => now()->format('H:i:s'),
            'attend_status'    => $status,
            'is_valid'         => true,
            'activity'         => $activityText,
        ]);

        $this->checkAttendanceStatus();
    }

    public function saveJournal()
    {
        $this->validate([
            'activity'      => 'required|min:10',
            'activityPhoto' => 'required|image|max:5120',
        ]);

        if ($this->todayJournal) {
            $photoPath = $this->activityPhoto->store('journal_photos', 'public');

            $this->todayJournal->update([
                'activity'   => $this->activity,
                'photo_path' => $photoPath,
            ]);

            $this->activity = '';
            $this->activityPhoto = null;
            $this->checkAttendanceStatus();
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function render()
    {
        $recap = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Libur' => 0, 'Alpha' => 0];
        $recentJournals = collect();

        if ($this->placement) {
            $startDate = Carbon::parse($this->placement->start_date);
            $endDate = Carbon::parse($this->placement->end_date);

            // =========================================================
            // REVISI: Ambil data jurnal HANYA selama rentang waktu PKL
            // =========================================================
            $allJournals = Journal::where('pkl_placement_id', $this->placement->id)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get();

            $recap['Hadir'] = $allJournals->where('attend_status', 'Hadir')->where('is_valid', true)->count();
            $recap['Izin']  = $allJournals->where('attend_status', 'Izin')->count();
            $recap['Sakit'] = $allJournals->where('attend_status', 'Sakit')->count();
            $recap['Libur'] = $allJournals->where('attend_status', 'Libur')->count();

            // Hitung ALPHA (Hari Kerja Senin-Jumat dalam rentang PKL)
            $today = today();
            $limitDate = $today->lessThan($endDate) ? $today : $endDate;

            $workingDays = 0;
            if ($startDate->lessThanOrEqualTo($limitDate)) {
                $workingDays = $startDate->diffInDaysFiltered(function (Carbon $date) {
                    return !$date->isWeekend(); // Hitung hari Senin-Jumat saja
                }, $limitDate) + 1; // +1 untuk include hari ini
            }

            // Karena $allJournals sudah di-filter based on PKL dates,
            // $loggedDays tidak akan bocor ngitung absen di luar jadwal PKL!
            $loggedDays = $recap['Hadir'] + $recap['Izin'] + $recap['Sakit'] + $recap['Libur'];
            $recap['Alpha'] = max(0, $workingDays - $loggedDays);

            // =========================================================
            // AMBIL 5 DATA HISTORY TERAKHIR (Tetap tampilkan semua data terbaru)
            // =========================================================
            $recentJournals = Journal::where('pkl_placement_id', $this->placement->id)
                ->orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->take(7)
                ->get()
                ->map(function ($j) {
                    $j->attendance_photo_url = $j->attendance_photo_path ? asset('storage/' . $j->attendance_photo_path) : null;
                    $j->activity_photo_url = $j->photo_path ? asset('storage/' . $j->photo_path) : null;
                    $j->formatted_date = Carbon::parse($j->date)->isoFormat('dddd, D MMM YYYY');
                    $j->formatted_time = Carbon::parse($j->time)->format('H:i');
                    return $j;
                });
        }

        return view('livewire.student.absensi', [
            'recentJournals' => $recentJournals,
            'recap' => $recap // Kirim data rekapan ke frontend
        ]);
    }
}
