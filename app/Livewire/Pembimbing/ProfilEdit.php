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
#[Title('Edit Profil Guru - GrisaPKL')]
class ProfilEdit extends Component
{
    public $phone;
    public $subject;

    public function mount()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        $this->phone = $teacher->phone ?? '';
        $this->subject = $teacher->subject ?? '';
    }

    public function saveProfile($signatureBase64 = null)
    {
        $this->validate([
            'phone' => 'required|string|max:20',
            'subject' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        $teacher = Teacher::firstOrCreate(['user_id' => $user->id]);

        $teacher->phone = $this->phone;
        $teacher->subject = $this->subject;

        if ($signatureBase64 && str_starts_with($signatureBase64, 'data:image')) {
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

        session()->flash('success', 'Data Profil & Tanda Tangan berhasil diperbarui!');
        return redirect()->route('pembimbing.profil');
    }

    public function render()
    {
        return view('livewire.pembimbing.profil-edit');
    }
}
