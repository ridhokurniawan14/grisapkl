<?php

namespace App\Livewire\Pembimbing;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Monitoring;
use App\Models\PklPlacement;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Edit Monitoring - GrisaPKL')]
class LaporEdit extends Component
{
    use WithFileUploads;

    public $monitoringId;
    public $dudika_id;
    public $notes;
    public $date;
    public $existingPhotos = [];
    public $newPhotos = []; // Untuk upload foto tambahan
    public $dudikaList = [];

    public function mount($id)
    {
        $this->monitoringId = $id;
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        // 1. Ambil Data Monitoring
        $monitoring = Monitoring::findOrFail($id);

        // Pastikan guru ini yang punya akses ke laporan ini
        if ($monitoring->teacher_id !== $teacher->id) {
            abort(403);
        }

        $this->dudika_id = $monitoring->dudika_id;
        $this->notes = $monitoring->notes;
        $this->date = $monitoring->date;

        // Decode foto lama
        if (!empty($monitoring->photo_path)) {
            $decoded = json_decode($monitoring->photo_path, true);
            $this->existingPhotos = is_array($decoded) ? $decoded : [$monitoring->photo_path];
        }

        // 2. Ambil List DUDIKA untuk pilihan (Hanya yang dibimbing guru ini)
        $this->dudikaList = PklPlacement::with('dudika')
            ->where('teacher_id', $teacher->id)
            ->where('status', 'Aktif')
            ->get()
            ->pluck('dudika.name', 'dudika_id')
            ->unique();
    }

    public function removeExistingPhoto($index)
    {
        unset($this->existingPhotos[$index]);
        $this->existingPhotos = array_values($this->existingPhotos);
    }

    public function updateMonitoring()
    {
        $this->validate([
            'dudika_id' => 'required',
            'notes' => 'required|min:10',
            'newPhotos.*' => 'nullable|image|max:2048', // Max 2MB per foto
        ]);

        $monitoring = Monitoring::findOrFail($this->monitoringId);

        // Gabungkan foto lama yang tidak dihapus dengan foto baru
        $finalPhotos = $this->existingPhotos;

        if ($this->newPhotos) {
            foreach ($this->newPhotos as $photo) {
                $path = $photo->store('monitoring', 'public');
                $finalPhotos[] = $path;
            }
        }

        $monitoring->update([
            'dudika_id' => $this->dudika_id,
            'notes' => $this->notes,
            'photo_path' => json_encode($finalPhotos),
        ]);

        session()->flash('success', 'Laporan monitoring berhasil diperbarui!');
        return redirect()->route('pembimbing.lapor');
    }

    public function render()
    {
        return view('livewire.pembimbing.lapor-edit');
    }
}
