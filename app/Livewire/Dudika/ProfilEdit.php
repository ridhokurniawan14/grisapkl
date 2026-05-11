<?php

namespace App\Livewire\Dudika;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Dudika;

#[Layout('components.layouts.app')]
#[Title('Edit Profil DUDIKA - GrisaPKL')]
class ProfilEdit extends Component
{
    // Properti Data DUDIKA
    public $name;
    public $address;
    public $head_name;
    public $head_nip;
    public $supervisor_name;
    public $supervisor_nip;
    public $supervisor_phone;

    public function mount()
    {
        $user = Auth::user();
        $dudika = Dudika::where('user_id', $user->id)->first();

        // Load data jika sudah ada
        if ($dudika) {
            $this->name = $dudika->name;
            $this->address = $dudika->address;
            $this->head_name = $dudika->head_name;
            $this->head_nip = $dudika->head_nip;
            $this->supervisor_name = $dudika->supervisor_name;
            $this->supervisor_nip = $dudika->supervisor_nip;
            $this->supervisor_phone = $dudika->supervisor_phone;
        }
    }

    public function saveProfile()
    {
        // Validasi input
        $this->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'head_name' => 'required|string|max:255',
            'head_nip' => 'nullable|string|max:255',
            'supervisor_name' => 'required|string|max:255',
            'supervisor_nip' => 'nullable|string|max:255',
            'supervisor_phone' => 'required|string|max:255',
        ], [
            'required' => 'Kolom ini wajib diisi.',
        ]);

        $user = Auth::user();

        // Update atau buat baru
        $dudika = Dudika::firstOrCreate(['user_id' => $user->id]);

        $dudika->update([
            'name' => $this->name,
            'address' => $this->address,
            'head_name' => $this->head_name,
            'head_nip' => $this->head_nip,
            'supervisor_name' => $this->supervisor_name,
            'supervisor_nip' => $this->supervisor_nip,
            'supervisor_phone' => $this->supervisor_phone,
        ]);

        session()->flash('success', 'Data Profil Instansi berhasil diperbarui!');
        return redirect()->route('dudika.profil');
    }

    public function render()
    {
        return view('livewire.dudika.profil-edit');
    }
}
