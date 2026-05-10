<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads; // WAJIB UNTUK UPLOAD
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PklPlacement;

#[Layout('components.layouts.app')]
#[Title('Profil Siswa - PKL Connect')]
class Profil extends Component
{
    use WithFileUploads;

    public $pklPlacement;
    public $student;
    public $newProfilePhoto; // Penampung foto baru

    public function mount()
    {
        $this->student = \App\Models\Student::with('studentClass')
            ->where('user_id', Auth::id())
            ->first();

        // Ambil data PKL placement siswa login
        $this->pklPlacement = PklPlacement::where(
            'student_id',
            $this->student?->id
        )->first();
    }

    // Fungsi otomatis berjalan saat siswa memilih foto baru
    public function updatedNewProfilePhoto()
    {
        $this->validate([
            'newProfilePhoto' => 'image|max:2048', // Maksimal 2MB
        ]);

        if ($this->student) {
            // Hapus foto lama jika ada
            if ($this->student->avatar && Storage::disk('public')->exists($this->student->avatar)) {
                Storage::disk('public')->delete($this->student->avatar);
            }

            // Simpan foto baru
            $path = $this->newProfilePhoto->store('students/avatars', 'public');

            // Simpan ke database (Pastikan di tabel students ada kolom 'avatar' atau 'photo_path')
            // Ubah 'avatar' menjadi nama kolom fotomu yang sebenarnya jika berbeda
            $this->student->update([
                'avatar' => $path
            ]);

            // Refresh data
            $this->student->refresh();
        }
    }
    // Fungsi untuk menerima gambar dari Kamera Live (Base64)
    public function saveBase64Photo($base64Data)
    {
        if ($this->student) {
            // Pecah data base64
            $image_parts = explode(";base64,", $base64Data);
            $image_base64 = base64_decode($image_parts[1]);

            // Buat nama file unik
            $filename = 'students/avatars/' . uniqid() . '.jpg';

            // Simpan ke storage public
            Storage::disk('public')->put($filename, $image_base64);

            // Hapus foto lama jika ada
            if ($this->student->avatar && Storage::disk('public')->exists($this->student->avatar)) {
                Storage::disk('public')->delete($this->student->avatar);
            }

            // Update ke database
            $this->student->update([
                'avatar' => $filename
            ]);

            $this->student->refresh();
        }
    }
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.student.profil');
    }
}
