<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\SchoolProfile;

#[Layout('components.layouts.guest')]
#[Title('Masuk - Grisa PKL')]
class LoginUniversal extends Component
{
    public $identifier = '';
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
            } elseif ($user->hasRole('dudika')) {
                return redirect()->to('/dudika/dashboard');
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

        if (Auth::attempt(['email' => $this->identifier, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            $user = Auth::user();

            if ($user->hasRole(['super_admin', 'humas'])) {
                return redirect()->to('/admin');
            } elseif ($user->hasRole('siswa')) {
                return redirect()->to('/siswa/absen');
            } elseif ($user->hasRole('dudika')) {
                return redirect()->to('/dudika/dashboard');
            } else {
                return redirect()->to('/pembimbing/dashboard');
            }
        }

        $this->addError('identifier', 'Kredensial tidak cocok dengan data kami.');
    }

    public function render()
    {
        $school   = SchoolProfile::first();
        $logoUrl  = ($school && $school->logo_path) ? asset('storage/' . $school->logo_path) : null;

        // Pass faviconUrl ke layout via share (pakai ViewComposer atau langsung)
        view()->share('faviconUrl', $logoUrl);

        return view('livewire.auth.login-universal', compact('logoUrl'));
    }
}
