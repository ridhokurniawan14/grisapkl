<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads; // WAJIB UNTUK UPLOAD FOTO
use App\Models\Journal;
use App\Models\PklPlacement;
use App\Models\SchoolProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Layout('components.layouts.app')]
#[Title('Absensi - PKL Connect')]
class Absensi extends Component
{
    use WithFileUploads;

    public $placement;
    public $hasAttendedToday = false;
    public $todayJournal;

    // Properti Form Jurnal
    public $activity = '';
    public $activityPhoto; // Properti untuk foto kegiatan

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

    // ==========================================
    // 1. FUNGSI CEK RADIUS DARI ALPINE.JS
    // ==========================================
    public function verifyLocation($lat, $lng)
    {
        $school = SchoolProfile::first();

        // Jika fitur radius aktif dari Humas
        if ($school && $school->is_radius_attendance_enabled) {
            $placementLat = $this->placement->latitude;
            $placementLng = $this->placement->longitude;
            $maxRadius = $this->placement->radius ?? 50;

            // Jika koordinat PKL belum diatur, izinkan saja (bypass)
            if (!$placementLat || !$placementLng) return true;

            $distance = $this->calculateDistance($lat, $lng, $placementLat, $placementLng);

            // Jika lebih dari radius, kembalikan FALSE (Ditolak)
            if ($distance > $maxRadius) return false;
        }

        return true; // Valid / Dalam Radius
    }

    // ==========================================
    // 2. FUNGSI ABSEN MASUK (HADIR) FOTO WAJAH
    // ==========================================
    public function submitAttendance($photoBase64, $lat, $lng)
    {
        if (!$this->placement || $this->hasAttendedToday) return;

        // Validasi keamanan lapis dua di backend
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
            'activity'              => '', // Kosong agar memicu Form Jurnal
        ]);

        $this->checkAttendanceStatus();
    }

    // ==========================================
    // 3. FUNGSI TOMBOL IZIN / SAKIT / LIBUR
    // ==========================================
    public function markAttendance($status)
    {
        if (!$this->placement || $this->hasAttendedToday) return;

        // Jika Libur, langsung isi activity dengan 'Libur' agar form jurnal TIDAK MUNCUL
        // Jika Izin/Sakit, isi dengan '' (string kosong) agar form jurnal MUNCUL
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

    // ==========================================
    // 4. FUNGSI SIMPAN JURNAL (TEKS + FOTO KEGIATAN)
    // ==========================================
    public function saveJournal()
    {
        $this->validate([
            'activity'      => 'required|min:10',
            'activityPhoto' => 'required|image|max:5120', // Wajib Foto, max 5MB
        ]);

        if ($this->todayJournal) {
            // Upload Foto Kegiatan
            $photoPath = $this->activityPhoto->store('journal_photos', 'public');

            $this->todayJournal->update([
                'activity'   => $this->activity,
                'photo_path' => $photoPath,
            ]);

            // Bersihkan form
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
        return view('livewire.student.absensi');
    }
}
