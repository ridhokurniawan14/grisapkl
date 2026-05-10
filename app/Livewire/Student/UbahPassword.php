<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

#[Layout('components.layouts.app')]
#[Title('Ubah Password - GrisaPKL')]
class UbahPassword extends Component
{
    public $current_password;
    public $password;
    public $password_confirmation;

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = Auth::user();

        // Cek apakah password saat ini benar
        if (!Hash::check($this->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password saat ini yang Anda masukkan salah.'
            ]);
        }

        // Update password baru
        $user->update([
            'password' => Hash::make($this->password)
        ]);

        // Beri notifikasi sukses dan kembalikan ke profil
        session()->flash('success', 'Password Anda berhasil diubah!');
        return redirect()->route('siswa.profil');
    }

    public function render()
    {
        return view('livewire.student.ubah-password');
    }
}
