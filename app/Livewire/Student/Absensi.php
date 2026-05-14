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

    const DIR_ATTENDANCE = 'journals/attendance';
    const DIR_ACTIVITY   = 'journals';

    public $placement;
    public $hasAttendedToday = false; // Akan di-update otomatis setiap render
    public $isRadiusEnabled = true;

    public $activity = '';
    public $activityPhoto;

    public function mount()
    {
        $school = SchoolProfile::first();
        $this->isRadiusEnabled = $school ? (bool) $school->is_radius_attendance_enabled : true;

        $this->placement = PklPlacement::whereHas('student', function ($q) {
            $q->where('user_id', Auth::id());
        })->where('status', 'Aktif')->first();

        // Pengecekan awal saat halaman dimuat
        if ($this->placement) {
            $this->checkAttendanceStatus();
        }
    }

    /**
     * Fungsi ini sekarang dipanggil di mount DAN render 
     * agar status tombol selalu sinkron dengan database (Real-time)
     */
    public function checkAttendanceStatus()
    {
        if (!$this->placement) return;

        $todayJournal = Journal::where('pkl_placement_id', $this->placement->id)
            ->whereDate('date', today())
            ->first();

        $this->hasAttendedToday = (bool) $todayJournal;
    }

    public function verifyLocation($lat, $lng): bool
    {
        try {
            if (!$this->isRadiusEnabled) return true;

            $placementLat = $this->placement->latitude  ?? null;
            $placementLng = $this->placement->longitude ?? null;
            $maxRadius    = $this->placement->radius    ?? 50;

            if (!$placementLat || !$placementLng) return true;

            $distance = $this->calculateDistance($lat, $lng, $placementLat, $placementLng);
            return $distance <= $maxRadius;
        } catch (\Throwable $e) {
            return true;
        }
    }

    public function submitAttendance($photoBase64, $lat, $lng)
    {
        if (!$this->placement || $this->hasAttendedToday) return;
        if (!$this->verifyLocation($lat, $lng)) return;

        $imageParts  = explode(';base64,', $photoBase64);
        $imageType   = explode('image/', $imageParts[0])[1] ?? 'png';
        $imageBase64 = base64_decode($imageParts[1]);
        $fileName    = self::DIR_ATTENDANCE . '/' . Str::uuid() . '.' . $imageType;

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

    public function updatedActivityPhoto()
    {
        $this->resetValidation('activityPhoto');
    }

    public function saveJournal()
    {
        $this->validate([
            'activity'      => 'required|min:10',
            'activityPhoto' => 'required|image|max:15360',
        ]);

        $todayJournal = Journal::where('pkl_placement_id', $this->placement->id)
            ->whereDate('date', today())
            ->first();

        if (!$todayJournal) return;

        if ($todayJournal->photo_path) {
            $this->deleteOldFile($todayJournal->photo_path);
        }

        $filename = Str::uuid() . '.' . $this->activityPhoto->getClientOriginalExtension();
        $photoPath = self::DIR_ACTIVITY . '/' . $filename;

        try {
            $sourcePath = $this->activityPhoto->getRealPath();
            list($width, $height, $type) = getimagesize($sourcePath);

            // ... (Kode kompresi gambar native kamu yang sudah jalan) ...
            if ($type == IMAGETYPE_PNG) {
                $source = imagecreatefrompng($sourcePath);
                imagealphablending($source, false);
                imagesavealpha($source, true);
            } else {
                $source = imagecreatefromjpeg($sourcePath);
                if (function_exists('exif_read_data')) {
                    $exif = @exif_read_data($sourcePath);
                    if ($exif && isset($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 3:
                                $source = imagerotate($source, 180, 0);
                                break;
                            case 6:
                                $source = imagerotate($source, 270, 0);
                                $tmp = $width;
                                $width = $height;
                                $height = $tmp;
                                break;
                            case 8:
                                $source = imagerotate($source, 90, 0);
                                $tmp = $width;
                                $width = $height;
                                $height = $tmp;
                                break;
                        }
                    }
                }
            }

            if ($width > 1024) {
                $newWidth = 1024;
                $newHeight = (int)(($height / $width) * $newWidth);
                $thumb = imagecreatetruecolor($newWidth, $newHeight);
                if ($type == IMAGETYPE_PNG) {
                    imagealphablending($thumb, false);
                    imagesavealpha($thumb, true);
                }
                imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                ob_start();
                if ($type == IMAGETYPE_PNG) imagepng($thumb, null, 8);
                else imagejpeg($thumb, null, 75);
                $imageContent = ob_get_clean();
                imagedestroy($thumb);
            } else {
                ob_start();
                if ($type == IMAGETYPE_PNG) imagepng($source, null, 8);
                else imagejpeg($source, null, 75);
                $imageContent = ob_get_clean();
            }
            imagedestroy($source);
            Storage::disk('public')->put($photoPath, $imageContent);
        } catch (\Exception $e) {
            $this->activityPhoto->storeAs(self::DIR_ACTIVITY, $filename, 'public');
        }

        $todayJournal->update([
            'activity'   => $this->activity,
            'photo_path' => $photoPath,
        ]);

        $this->activity      = '';
        $this->activityPhoto = null;
        $this->checkAttendanceStatus();
    }

    private function deleteOldFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a    = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public function render()
    {
        $recap          = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Libur' => 0, 'Alpha' => 0];
        $recentJournals = collect();
        $todayJournal   = null;

        if ($this->placement) {

            // PERBAIKAN FATAL: Jalankan pengecekan status SETIAP RENDER agar wire:poll bisa mendeteksi hapus data
            $this->checkAttendanceStatus();

            $todayJournal = Journal::where('pkl_placement_id', $this->placement->id)
                ->whereDate('date', today())
                ->first();

            $allJournals = Journal::where('pkl_placement_id', $this->placement->id)->get();

            // ... (Kode rekap absensi kamu tetap sama di sini) ...
            $recap['Hadir'] = $allJournals->where('attend_status', 'Hadir')->count();
            $recap['Izin']  = $allJournals->where('attend_status', 'Izin')->count();
            $recap['Sakit'] = $allJournals->where('attend_status', 'Sakit')->count();
            $recap['Libur'] = $allJournals->where('attend_status', 'Libur')->count();

            if ($this->placement->start_date && $this->placement->end_date) {
                $startDate = Carbon::parse($this->placement->start_date)->startOfDay();
                $endDate   = Carbon::parse($this->placement->end_date)->endOfDay();
                $today     = Carbon::now()->endOfDay();
                $limitDate = $today->lessThan($endDate) ? $today : $endDate;
                $workingDays = 0;
                if ($startDate->lessThanOrEqualTo($limitDate)) {
                    $period = \Carbon\CarbonPeriod::create($startDate, $limitDate);
                    foreach ($period as $date) {
                        if ($date->isWeekday()) $workingDays++;
                    }
                }
                $loggedDays = $allJournals->whereBetween('date', [$startDate->format('Y-m-d'), $limitDate->format('Y-m-d')])
                    ->filter(fn($j) => Carbon::parse($j->date)->isWeekday())
                    ->pluck('date')->unique()->count();
                $recap['Alpha'] = max(0, $workingDays - $loggedDays);
            }

            $recentJournals = Journal::where('pkl_placement_id', $this->placement->id)
                ->orderBy('date', 'desc')->orderBy('time', 'desc')->take(7)->get()
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
            'recap'          => $recap,
            'todayJournal'   => $todayJournal,
        ]);
    }
}
