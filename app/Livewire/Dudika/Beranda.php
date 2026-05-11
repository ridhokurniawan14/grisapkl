<?php

namespace App\Livewire\Dudika;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Beranda DUDIKA - GrisaPKL')]
class Beranda extends Component
{
    public $search = ''; // Variabel penampung kata kunci pencarian

    public function render()
    {
        // 1. Sapaan Berdasarkan Jam Lokal (WIB)
        $hour = Carbon::now('Asia/Jakarta')->format('H');
        if ($hour >= 4 && $hour < 11) {
            $greeting = 'Selamat Pagi,';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat Siang,';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat Sore,';
        } else {
            $greeting = 'Selamat Malam,';
        }

        // =========================================================
        // DATA DUMMY (Untuk Testing UI DUDIKA)
        // =========================================================
        $dudikaName = 'Bpk. Hendra S.';
        $isComplete = false;

        $students = collect([
            [
                'id' => 1,
                'name' => 'Ahmad Dani',
                'field' => 'Software Engineer Intern',
                'avatar' => null,
                'recap' => ['H' => 22, 'I' => 0, 'S' => 0, 'L' => 4, 'A' => 0],
                'phone' => '6281234567890'
            ],
            [
                'id' => 2,
                'name' => 'Siti Aminah',
                'field' => 'Digital Marketing Intern',
                'avatar' => null,
                'recap' => ['H' => 18, 'I' => 2, 'S' => 1, 'L' => 4, 'A' => 1],
                'phone' => '6281298765432'
            ],
            [
                'id' => 3,
                'name' => 'Budi Santoso',
                'field' => 'Teknisi Jaringan',
                'avatar' => null,
                'recap' => ['H' => 15, 'I' => 0, 'S' => 5, 'L' => 4, 'A' => 2],
                'phone' => '6281211112222'
            ]
        ]);

        // 2. LOGIKA PENCARIAN (Filter Data Siswa)
        $filteredStudents = $students->filter(function ($siswa) {
            $matchSearch = empty($this->search)
                || stripos($siswa['name'], $this->search) !== false
                || stripos($siswa['field'], $this->search) !== false;
            return $matchSearch;
        });

        return view('livewire.dudika.beranda', [
            'greeting' => $greeting,
            'dudikaName' => $dudikaName,
            'isComplete' => $isComplete,
            'students' => $filteredStudents,
        ]);
    }
}
