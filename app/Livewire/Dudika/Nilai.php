<?php

namespace App\Livewire\Dudika;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\PklPlacement;
use App\Models\PklAssessment;
use App\Models\PklAssessmentScore; // Tambahan Model Score
use App\Models\Dudika;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
#[Title('Penilaian Siswa - GrisaPKL')]
class Nilai extends Component
{
    public $search = '';

    public function render()
    {
        $dudika = Dudika::where('user_id', Auth::id())->first();

        $students = collect();
        $totalStudents = 0;
        $gradedCount = 0;
        $progressPercent = 0;

        if ($dudika) {
            $placements = PklPlacement::with(['student.user', 'student.studentClass.major'])
                ->where('dudika_id', $dudika->id)
                ->where('status', 'Aktif')
                ->get();

            $totalStudents = $placements->count();

            $students = $placements->map(function ($p) use (&$gradedCount) {
                // Cek apakah sudah ada penilaian
                $assessment = PklAssessment::where('pkl_placement_id', $p->id)->first();
                $avg = 0;

                if ($assessment) {
                    $gradedCount++;
                    // Hitung rata-rata murni dari tabel pkl_assessment_scores
                    $avgValue = PklAssessmentScore::where('pkl_assessment_id', $assessment->id)->avg('score');
                    $avg = $avgValue ? round($avgValue, 1) : 0;
                }

                $student = $p->student;

                // LOGIKA AVATAR SUPER KEBAL
                $avatarPath = null;
                if (!empty($student->user->avatar)) {
                    $avatarPath = asset('storage/' . $student->user->avatar);
                } elseif (!empty($student->user->avatar_path)) {
                    $avatarPath = asset('storage/' . $student->user->avatar_path);
                } elseif (!empty($student->avatar)) {
                    $avatarPath = asset('storage/' . $student->avatar);
                } elseif (!empty($student->avatar_path)) {
                    $avatarPath = asset('storage/' . $student->avatar_path);
                }

                return [
                    'placement_id'  => $p->id,
                    'name'          => $student->user->name ?? $student->name,
                    'field'         => $p->pkl_field ?? ($student->studentClass->major->name ?? 'Jurusan Umum'),
                    'avatar'        => $avatarPath, // Sudah berupa URL Lengkap
                    'status'        => $assessment ? 'Sudah Dinilai' : 'Belum Dinilai',
                    'average_score' => $avg
                ];
            });

            $progressPercent = $totalStudents > 0 ? round(($gradedCount / $totalStudents) * 100) : 0;
        }

        $filteredStudents = $students->filter(function ($siswa) {
            return empty($this->search)
                || stripos($siswa['name'], $this->search) !== false
                || stripos($siswa['field'], $this->search) !== false;
        });

        return view('livewire.dudika.nilai', [
            'students' => $filteredStudents,
            'totalStudents' => $totalStudents,
            'gradedCount' => $gradedCount,
            'progressPercent' => $progressPercent,
        ]);
    }
}
