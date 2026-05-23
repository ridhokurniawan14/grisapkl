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
                return redirect()->to('/dudika/beranda');
            } else {
                return redirect()->to('/pembimbing/beranda');
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

        // Login dulu
        if (!Auth::attempt([
            'email' => $this->identifier,
            'password' => $this->password,
        ], $this->remember)) {

            $this->addError('identifier', 'Kredensial tidak cocok dengan data kami.');
            return;
        }

        session()->regenerate();

        $user = Auth::user();

        /*
    |--------------------------------------------------------------------------
    | CEK AKSES SISWA
    |--------------------------------------------------------------------------
    */
        if ($user->hasRole('siswa')) {

            $student = $user->student;

            // Kalau data student tidak ada
            if (!$student) {
                Auth::logout();

                $this->addError('identifier', 'Data siswa tidak ditemukan.');
                return;
            }

            // Kalau akses dimatikan
            if (!$student->is_active) {

                Auth::logout();

                $this->addError(
                    'identifier',
                    'Akses login siswa dinonaktifkan. Silahkan hubungi Wali Kelas.'
                );

                return;
            }

            return redirect()->to('/siswa/absen');
        }

        /*
    |--------------------------------------------------------------------------
    | ROLE LAIN
    |--------------------------------------------------------------------------
    */
        if ($user->hasRole(['super_admin', 'humas'])) {
            return redirect()->to('/admin');
        }

        if ($user->hasRole('dudika')) {
            return redirect()->to('/dudika/beranda');
        }

        return redirect()->to('/pembimbing/beranda');
    }

    public function render()
    {
        $school   = SchoolProfile::first();
        $logoUrl  = ($school && $school->app_logo_path) ? asset('storage/' . $school->app_logo_path) : null;

        // Pass faviconUrl ke layout via share (pakai ViewComposer atau langsung)
        view()->share('faviconUrl', $logoUrl);

        return view('livewire.auth.login-universal', compact('logoUrl'));
    }
}
