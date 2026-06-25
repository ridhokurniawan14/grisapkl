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

    const DIR_ATTENDANCE = 'journals/attendance';
    const DIR_ACTIVITY   = 'journals';

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

        $this->journalId      = $journal->id;
        $this->attend_status  = $journal->attend_status;
        $this->originalStatus = $journal->attend_status;
        $this->activity       = $journal->activity ?? '';
    }

    public function verifyLocation(float $lat, float $lng): bool
    {
        try {
            $school  = SchoolProfile::first();
            $journal = Journal::with('pklPlacement')->find($this->journalId);

            if (!$journal || !$journal->pklPlacement) return true;

            $placement = $journal->pklPlacement;

            if ($school && $school->is_radius_attendance_enabled) {
                $placementLat = $placement->latitude  ?? null;
                $placementLng = $placement->longitude ?? null;
                $maxRadius    = $placement->radius    ?? 50;

                if (!$placementLat || !$placementLng) return true;

                $distance = $this->calculateDistance($lat, $lng, $placementLat, $placementLng);
                if ($distance > $maxRadius) return false;
            }

            return true;
        } catch (\Throwable $e) {
            return true;
        }
    }

    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a    = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public function updateWithCamera(string $photoBase64, float $lat, float $lng, bool $checkRadius = true): bool
    {
        $this->validateData();

        if ($checkRadius && !$this->verifyLocation($lat, $lng)) return false;

        $imageParts  = explode(';base64,', $photoBase64);
        $imageType   = explode('image/', $imageParts[0])[1] ?? 'png';
        $imageBase64 = base64_decode($imageParts[1]);
        $fileName    = self::DIR_ATTENDANCE . '/' . Str::uuid() . '.' . $imageType;

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

    public function updatedActivityPhoto()
    {
        $this->resetValidation('activityPhoto');
    }

    private function validateData(): void
    {
        $rules    = ['attend_status' => 'required|in:Hadir,Izin,Sakit,Libur'];
        $messages = [];

        if (in_array($this->attend_status, ['Hadir', 'Izin', 'Sakit'])) {
            $rules['activity']             = 'required|min:10';
            $messages['activity.required'] = 'Keterangan aktivitas wajib diisi.';
            $messages['activity.min']      = 'Keterangan minimal 10 karakter.';

            $journal = Journal::find($this->journalId);

            if (!$journal?->photo_path) {
                // Biarkan validasi max besar, karena yang diterima server sudah kecil berkat JS
                $rules['activityPhoto']             = 'required|image|max:15360';
                $messages['activityPhoto.required'] = 'Foto kegiatan wajib diupload untuk status ' . $this->attend_status . '.';
            } elseif ($this->activityPhoto) {
                $rules['activityPhoto'] = 'image|max:15360';
            }

            $messages['activityPhoto.image'] = 'File harus berupa gambar (jpg, png, dll).';
            $messages['activityPhoto.max']   = 'Ukuran foto terlalu besar. Silakan pilih foto lain.';
        }

        $this->validate($rules, $messages);
    }

    private function processUpdate(?string $attendancePhotoPath = null, ?float $lat = null, ?float $lng = null): void
    {
        $journal = Journal::find($this->journalId);
        $data    = ['attend_status' => $this->attend_status];

        $newActivityPhotoPath = null;
        if ($this->attend_status !== 'Libur' && $this->activityPhoto) {
            $this->deleteOldFile($journal->photo_path);

            // MANTRA SAKTI: Karena foto sudah dikompres super kecil di HP Siswa via Javascript, 
            // Kita hapus semua proses GD Library yang berat, dan langsung simpan file-nya!
            $filename = Str::uuid() . '.jpg';
            $newActivityPhotoPath = $this->activityPhoto->storeAs(self::DIR_ACTIVITY, $filename, 'public');
        }

        if ($this->attend_status === 'Libur') {
            $this->deleteOldFile($journal->attendance_photo_path);
            $this->deleteOldFile($journal->photo_path);

            $data['activity']              = 'Libur / Tanggal Merah';
            $data['attendance_photo_path'] = null;
            $data['photo_path']            = null;
            $data['latitude']              = null;
            $data['longitude']             = null;
        } elseif ($this->attend_status === 'Hadir') {
            $data['activity'] = $this->activity;

            if ($attendancePhotoPath) {
                $this->deleteOldFile($journal->attendance_photo_path);
                $data['attendance_photo_path'] = $attendancePhotoPath;
                $data['latitude']              = $lat;
                $data['longitude']             = $lng;
            }

            if ($newActivityPhotoPath) {
                $data['photo_path'] = $newActivityPhotoPath;
            }
        } else {
            $data['activity'] = $this->activity;

            if ($journal->attendance_photo_path) {
                $this->deleteOldFile($journal->attendance_photo_path);
                $data['attendance_photo_path'] = null;
            }

            if ($newActivityPhotoPath) {
                $data['photo_path'] = $newActivityPhotoPath;
            }
        }

        $journal->update($data);
    }

    private function deleteOldFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function render()
    {
        return view('livewire.student.jurnal-edit', [
            'journal' => Journal::find($this->journalId),
        ]);
    }
}
