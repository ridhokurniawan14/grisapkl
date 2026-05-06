<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
#[Title('Edit Profil - GrisaPKL')]
class ProfilEdit extends Component
{
    public $student;

    // Form Data Pribadi
    public $birth_place;
    public $birth_date;
    public $religion;
    public $phone;
    public $address;

    // Form Data Orang Tua
    public $father_name;
    public $father_job;
    public $mother_name;
    public $mother_job;
    public $parent_phone;
    public $parent_address;

    public function mount()
    {
        $this->student = \App\Models\Student::where('user_id', Auth::id())->first();

        // Isi properti dengan data yang sudah ada di database
        if ($this->student) {
            $this->birth_place = $this->student->birth_place;
            $this->birth_date = $this->student->birth_date;
            $this->religion = $this->student->religion;
            $this->phone = $this->student->phone;
            $this->address = $this->student->address;

            $this->father_name = $this->student->father_name;
            $this->father_job = $this->student->father_job;
            $this->mother_name = $this->student->mother_name;
            $this->mother_job = $this->student->mother_job;
            $this->parent_phone = $this->student->parent_phone;
            $this->parent_address = $this->student->parent_address;
        }
    }

    public function save()
    {
        // Validasi inputan
        $this->validate([
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'father_name' => 'nullable|string|max:255',
            'father_job' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_job' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'parent_address' => 'nullable|string',
        ]);

        if ($this->student) {
            $this->student->update([
                'birth_place' => $this->birth_place,
                'birth_date' => $this->birth_date,
                'religion' => $this->religion,
                'phone' => $this->phone,
                'address' => $this->address,
                'father_name' => $this->father_name,
                'father_job' => $this->father_job,
                'mother_name' => $this->mother_name,
                'mother_job' => $this->mother_job,
                'parent_phone' => $this->parent_phone,
                'parent_address' => $this->parent_address,
            ]);
        }

        // Kembali ke halaman profil setelah berhasil simpan
        return redirect()->route('siswa.profil');
    }

    public function render()
    {
        return view('livewire.student.profil-edit');
    }
}
