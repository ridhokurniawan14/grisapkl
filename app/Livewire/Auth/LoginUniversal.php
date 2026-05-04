<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.guest')] // MANTRA: Pakai cetakan Guest (tanpa nav bar)
#[Title('Masuk - Grisa PKL')]
class LoginUniversal extends Component
{
    public $identifier = ''; // Bisa email, NISN, atau NIP
    public $password = '';
    public $remember = false;

    public function mount()
    {
        // Cegat user yang iseng buka /login padahal sudah masuk
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasRole(['super_admin', 'humas'])) {
                return redirect()->to('/admin');
            } elseif ($user->hasRole('siswa')) {
                return redirect()->to('/siswa/absen');
            } else {
                return redirect()->to('/pembimbing/dashboard');
            }
        }
    }

    public function authenticate()
    {
        $this->validate([
            'identifier' => 'required',
            'password' => 'required',
        ], [
            'identifier.required' => 'Identitas wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // Coba login berdasarkan email (Nanti bisa di-expand buat cek NISN/NIP di database)
        if (Auth::attempt(['email' => $this->identifier, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            // Satpam mengecek Role dan mengarahkan
            $user = Auth::user();

            if ($user->hasRole(['super_admin', 'humas'])) {
                return redirect()->to('/admin');
            } elseif ($user->hasRole('siswa')) {
                return redirect()->to('/siswa/absen'); // Rute PWA Siswa
            } else {
                return redirect()->to('/pembimbing/dashboard'); // Rute Guru/Dudika
            }
        }

        // Kalau gagal login
        $this->addError('identifier', 'Kredensial tidak cocok dengan data kami.');
    }

    public function render()
    {
        return view('livewire.auth.login-universal');
    }
}
