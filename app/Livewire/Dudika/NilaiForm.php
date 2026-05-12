<?php

namespace App\Livewire\Dudika;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\PklPlacement;
use App\Models\PklAssessment;
use App\Models\PklAssessmentScore; // Model anak / relasi skor
use App\Models\AssessmentIndicator;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
#[Title('Form Penilaian Siswa - GrisaPKL')]
class NilaiForm extends Component
{
    public $placement_id;
    public $studentData = [];
    public $indicators = [];

    // Model Bindings
    public $scores = [];
    public $attendance_notes;
    public $assessment_notes;

    public function mount($placement_id)
    {
        $this->placement_id = $placement_id;

        $placement = PklPlacement::with(['student.user', 'student.studentClass', 'dudika'])->findOrFail($placement_id);

        $dudika = \App\Models\Dudika::where('user_id', Auth::id())->first();
        if (!$dudika || $placement->dudika_id !== $dudika->id) {
            abort(403, 'Akses ditolak.');
        }

        $student = $placement->student;

        // Logika Avatar
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

        $this->studentData = [
            'name' => $student->user->name ?? $student->name,
            'field' => $placement->pkl_field ?? 'Jurusan Umum',
            'class' => $student->studentClass->name ?? '-',
            'avatar' => $avatarPath, // Avatar Full URL
        ];

        // Ambil Indikator
        if ($placement->assessment_scheme_id) {
            $this->indicators = AssessmentIndicator::where('assessment_scheme_id', $placement->assessment_scheme_id)
                ->where('is_active', true)
                ->get();
        }

        // Cek apakah sudah pernah dinilai
        $assessment = PklAssessment::where('pkl_placement_id', $placement_id)->first();

        if ($assessment) {
            $this->attendance_notes = $assessment->attendance_notes;
            $this->assessment_notes = $assessment->assessment_notes;

            // Ambil data skor dari tabel child pkl_assessment_scores
            $savedScores = PklAssessmentScore::where('pkl_assessment_id', $assessment->id)
                ->pluck('score', 'assessment_indicator_id')->toArray();

            foreach ($this->indicators as $ind) {
                $this->scores[$ind->id] = $savedScores[$ind->id] ?? 85;
            }
        } else {
            foreach ($this->indicators as $ind) {
                $this->scores[$ind->id] = 85;
            }
        }
    }

    public function saveAssessment()
    {
        // Validasi
        $rules = [
            'attendance_notes' => 'nullable|string|max:1000',
            'assessment_notes' => 'nullable|string|max:1000',
        ];
        foreach ($this->indicators as $ind) {
            $rules["scores.{$ind->id}"] = 'required|numeric|min:85|max:100';
        }

        $this->validate($rules, [
            'scores.*.min' => 'Nilai minimal adalah 85',
            'scores.*.max' => 'Nilai maksimal adalah 100',
        ]);

        // 1. SIMPAN KE TABEL INDUK (Catatan)
        $assessment = PklAssessment::updateOrCreate(
            ['pkl_placement_id' => $this->placement_id],
            [
                'attendance_notes' => $this->attendance_notes,
                'assessment_notes' => $this->assessment_notes,
            ]
        );

        // 2. SIMPAN KE TABEL ANAK (Per Indikator)
        foreach ($this->scores as $indicator_id => $score) {
            PklAssessmentScore::updateOrCreate(
                [
                    'pkl_assessment_id' => $assessment->id,
                    'assessment_indicator_id' => $indicator_id
                ],
                [
                    'score' => $score
                ]
            );
        }

        session()->flash('success', 'Penilaian berhasil disimpan & tersinkronisasi!');
        return redirect()->route('dudika.nilai');
    }

    public function render()
    {
        return view('livewire.dudika.nilai-form');
    }
}
