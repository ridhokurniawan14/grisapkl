<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use App\Models\Journal;
use App\Models\SchoolProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Edit Jurnal - GrisaPKL')]
class JurnalEdit extends Component
{
    use WithFileUploads;

    public ?int $journalId = null;
    public $attend_status;
    public $activity = '';
    public $activityPhoto;
    public $originalStatus;

    public function mount($id)
    {
        $journal = Journal::whereHas('pklPlacement.student', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        if (Carbon::parse($journal->date)->diffInDays(now()) > 30) {
            abort(403, 'Jurnal sudah lebih dari 30 hari dan tidak dapat diedit lagi.');
        }

        $this->journalId = $journal->id;
        $this->attend_status = $journal->attend_status;
        $this->originalStatus = $journal->attend_status;
        $this->activity = $journal->activity ?? '';
    }

    public function verifyLocation($lat, $lng)
    {
        try {
            $school = SchoolProfile::first();
            $journal = Journal::with('pklPlacement')->find($this->journalId);
            if (!$journal || !$journal->pklPlacement) return true;

            $placement = $journal->pklPlacement;

            if ($school && $school->is_radius_attendance_enabled) {
                $placementLat = $placement->latitude;
                $placementLng = $placement->longitude;
                $maxRadius = $placement->radius ?? 50;

                if (!$placementLat || !$placementLng) return true;

                $distance = $this->calculateDistance((float)$lat, (float)$lng, (float)$placementLat, (float)$placementLng);
                if ($distance > $maxRadius) return false;
            }
            return true;
        } catch (\Exception $e) {
            return true; // Bypass jika ada error format agar sistem tidak meledak
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

    public function updateWithCamera($photoBase64, $lat, $lng, $checkRadius = true)
    {
        $this->validateData();

        if ($checkRadius) {
            if (!$this->verifyLocation($lat, $lng)) return false;
        }

        $imageParts = explode(";base64,", $photoBase64);
        $imageTypeAux = explode("image/", $imageParts[0]);
        $imageType = $imageTypeAux[1] ?? 'png';
        $imageBase64 = base64_decode($imageParts[1]);
        $fileName = 'attendance/' . Str::uuid() . '.' . $imageType;

        Storage::disk('public')->put($fileName, $imageBase64);

        $this->processUpdate($fileName, $lat, $lng);
        return true;
    }

    public function updateWithoutCamera()
    {
        $this->validateData();
        $this->processUpdate();
        return redirect()->route('siswa.jurnal');
    }

    private function validateData()
    {
        $rules = ['attend_status' => 'required|in:Hadir,Izin,Sakit,Libur'];
        if (in_array($this->attend_status, ['Hadir', 'Izin', 'Sakit'])) {
            $rules['activity'] = 'required|min:10';
        }
        if ($this->activityPhoto) {
            $rules['activityPhoto'] = 'image|max:5120';
        }
        $this->validate($rules);
    }

    private function processUpdate($attendancePhotoPath = null, $lat = null, $lng = null)
    {
        $journal = Journal::find($this->journalId);
        $data = ['attend_status' => $this->attend_status];

        if ($this->attend_status === 'Libur') {
            $data['activity'] = 'Libur / Tanggal Merah';
            $data['latitude'] = null;
            $data['longitude'] = null;
            $this->deleteOldFile($journal->attendance_photo_path);
            $this->deleteOldFile($journal->photo_path);
            $data['attendance_photo_path'] = null;
            $data['photo_path'] = null;
        } elseif (in_array($this->attend_status, ['Izin', 'Sakit'])) {
            $data['activity'] = $this->activity;
            $data['latitude'] = null;
            $data['longitude'] = null;
            $this->deleteOldFile($journal->attendance_photo_path);
            $data['attendance_photo_path'] = null;

            if ($this->activityPhoto) {
                $this->deleteOldFile($journal->photo_path);
                $data['photo_path'] = $this->activityPhoto->store('journal_photos', 'public');
            }
        } else {
            $data['activity'] = $this->activity;
            if ($attendancePhotoPath) {
                $this->deleteOldFile($journal->attendance_photo_path);
                $data['attendance_photo_path'] = $attendancePhotoPath;
                $data['latitude'] = $lat;
                $data['longitude'] = $lng;
            }
            if ($this->activityPhoto) {
                $this->deleteOldFile($journal->photo_path);
                $data['photo_path'] = $this->activityPhoto->store('journal_photos', 'public');
            }
        }
        $journal->update($data);
    }

    private function deleteOldFile($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function render()
    {
        return view('livewire.student.jurnal-edit', [
            'journal' => Journal::find($this->journalId)
        ]);
    }
}
