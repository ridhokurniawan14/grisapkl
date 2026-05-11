<?php

namespace App\Livewire\Dudika;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Dudika;

#[Layout('components.layouts.app')]
#[Title('Profil DUDIKA - GrisaPKL')]
class Profil extends Component
{
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
        $dudika = Dudika::where('user_id', $user->id)->first();

        // Siapkan Data (Gunakan fallback jika belum diisi)
        $dudikaData = [
            'name'             => $dudika->name ?? 'Nama Instansi Belum Diisi',
            'address'          => $dudika->address ?? 'Alamat Belum Lengkap',
            'head_name'        => $dudika->head_name ?? 'Belum Diisi',
            'head_nip'         => $dudika->head_nip ?? '-',
            'supervisor_name'  => $dudika->supervisor_name ?? 'Belum Diisi',
            'supervisor_nip'   => $dudika->supervisor_nip ?? '-',
            'supervisor_phone' => $dudika->supervisor_phone ?? 'Belum Diisi',
            'email'            => $user->email,
        ];

        return view('livewire.dudika.profil', [
            'dudikaData' => $dudikaData
        ]);
    }
}
