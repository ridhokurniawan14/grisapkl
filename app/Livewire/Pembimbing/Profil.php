<?php

namespace App\Livewire\Pembimbing;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Teacher;

#[Layout('components.layouts.app')]
#[Title('Profil Guru - GrisaPKL')]
class Profil extends Component
{
    public $isEditing = false;

    // Properti Form Edit
    public $phone;
    public $subject;

    public function mount()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        $this->phone = $teacher->phone ?? '';
        $this->subject = $teacher->subject ?? '';
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
    }

    public function saveProfile($signatureBase64 = null)
    {
        $this->validate([
            'phone' => 'required|string|max:20',
            'subject' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        // Buat record Teacher baru jika belum ada
        $teacher = Teacher::firstOrCreate(['user_id' => $user->id]);

        $teacher->phone = $this->phone;
        $teacher->subject = $this->subject;

        // Jika ada coretan TTD baru (dimulai dengan data:image)
        if ($signatureBase64 && strpos($signatureBase64, 'data:image') === 0) {
            // Hapus TTD lama jika ada
            if ($teacher->signature_path && Storage::disk('public')->exists($teacher->signature_path)) {
                Storage::disk('public')->delete($teacher->signature_path);
            }

            $imageParts = explode(";base64,", $signatureBase64);
            $imageTypeAux = explode("image/", $imageParts[0]);
            $imageType = $imageTypeAux[1] ?? 'png';
            $imageBase64 = base64_decode($imageParts[1]);
            $fileName = 'signatures/' . Str::uuid() . '.' . $imageType;

            Storage::disk('public')->put($fileName, $imageBase64);
            $teacher->signature_path = $fileName;
        }

        $teacher->save();

        $this->isEditing = false;
        session()->flash('success', 'Data Profil & Tanda Tangan berhasil diperbarui!');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    }

    public function render()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        $guruData = [
            'name' => $user->name,
            'nip' => $teacher->nip ?? null,
            'phone' => $teacher->phone ?? 'Belum Diisi',
            'subject' => $teacher->subject ?? 'Belum Diisi',
            'email' => $user->email,
            'signature_path' => $teacher->signature_path ?? null,
        ];

        return view('livewire.pembimbing.profil', compact('guruData'));
    }
}
