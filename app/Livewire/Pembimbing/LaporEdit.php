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
    public $existingPhoto;
    public $newPhoto;
    public $dudikaList = [];

    public function mount()
    {
        $id = request()->query('monitoring_id');
        abort_if(!$id, 404, 'Data monitoring tidak ditemukan.');

        $this->monitoringId = $id;
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        $monitoring = Monitoring::with('pklPlacement')->findOrFail($id);

        if ($monitoring->pklPlacement->teacher_id !== $teacher->id) {
            abort(403, 'Akses ditolak.');
        }

        $this->dudika_id = $monitoring->pklPlacement->dudika_id;
        $this->notes = $monitoring->activity;
        $this->date = $monitoring->date;

        // Antisipasi jika datanya JSON dari testing sebelumnya, kita convert ke string tunggal
        if (!empty($monitoring->photo_path)) {
            $decoded = json_decode($monitoring->photo_path, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->existingPhoto = $decoded[0] ?? null;
            } else {
                $this->existingPhoto = $monitoring->photo_path;
            }
        }

        $this->dudikaList = PklPlacement::with('dudika')
            ->where('teacher_id', $teacher->id)
            ->where('status', 'Aktif')
            ->get()
            ->pluck('dudika.name', 'dudika_id')
            ->unique();
    }

    public function updateMonitoring()
    {
        $this->validate([
            'dudika_id' => 'required',
            'notes' => 'required|min:10',
            'newPhoto' => 'nullable|image|max:2048',
        ]);

        $monitoring = Monitoring::with('pklPlacement')->findOrFail($this->monitoringId);

        $photoPathToSave = $this->existingPhoto;

        if ($this->newPhoto) {
            // PERBAIKAN: Simpan ke folder 'monitorings' (sesuai Filament) dan jadikan STRING biasa
            $photoPathToSave = $this->newPhoto->store('monitorings', 'public');
        }

        $placementIds = PklPlacement::where('dudika_id', $this->dudika_id)
            ->where('teacher_id', $monitoring->pklPlacement->teacher_id)
            ->pluck('id');

        Monitoring::whereIn('pkl_placement_id', $placementIds)
            ->where('date', $this->date)
            ->update([
                'activity' => $this->notes,
                'photo_path' => $photoPathToSave, // Disimpan sebagai String (bukan JSON encode)
            ]);

        session()->flash('success', 'Laporan monitoring berhasil diperbarui!');
        return redirect()->route('pembimbing.lapor');
    }

    public function render()
    {
        return view('livewire.pembimbing.lapor-edit');
    }
}
