<?php

namespace App\Livewire\Dudika;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Penilaian Siswa - GrisaPKL')]
class Nilai extends Component
{
    public $search = '';

    public function render()
    {
        // =========================================================
        // DATA DUMMY (Untuk Testing UI Penilaian DUDIKA)
        // =========================================================
        $students = collect([
            [
                'id' => 1,
                'name' => 'Aditya Pratama',
                'field' => 'Rekayasa Perangkat Lunak',
                'avatar' => null,
                'status' => 'Belum Dinilai',
                'average_score' => 0
            ],
            [
                'id' => 2,
                'name' => 'Siti Aminah',
                'field' => 'Teknik Komputer & Jaringan',
                'avatar' => null,
                'status' => 'Sudah Dinilai',
                'average_score' => 89.5
            ],
            [
                'id' => 3,
                'name' => 'Budi Santoso',
                'field' => 'Multimedia',
                'avatar' => null,
                'status' => 'Belum Dinilai',
                'average_score' => 0
            ],
            [
                'id' => 4,
                'name' => 'Rian Hidayat',
                'field' => 'Rekayasa Perangkat Lunak',
                'avatar' => null,
                'status' => 'Sudah Dinilai',
                'average_score' => 75.0
            ]
        ]);

        // Logika Pencarian
        $filteredStudents = $students->filter(function ($siswa) {
            return empty($this->search)
                || stripos($siswa['name'], $this->search) !== false
                || stripos($siswa['field'], $this->search) !== false;
        });

        // Hitung Statistik
        $totalStudents = $students->count();
        $gradedCount = $students->where('status', 'Sudah Dinilai')->count();
        $progressPercent = $totalStudents > 0 ? round(($gradedCount / $totalStudents) * 100) : 0;

        return view('livewire.dudika.nilai', [
            'students' => $filteredStudents,
            'totalStudents' => $totalStudents,
            'gradedCount' => $gradedCount,
            'progressPercent' => $progressPercent,
        ]);
    }
}
