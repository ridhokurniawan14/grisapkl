<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\PklPlacement;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
#[Title('DUDIKA - GrisaPKL')]
class Dudika extends Component
{
    public $placement;
    public $pkl_field;

    public function mount()
    {
        $this->loadPlacement();
    }

    public function loadPlacement()
    {
        $this->placement = PklPlacement::with(['dudika', 'teacher'])
            ->whereHas('student', function ($q) {
                $q->where('user_id', Auth::id());
            })->where('status', 'Aktif')->first();

        if ($this->placement) {
            $this->pkl_field = $this->placement->pkl_field;
        }
    }

    public function savePklField()
    {
        $this->validate([
            'pkl_field' => 'required|string|max:255',
        ]);

        if ($this->placement) {
            $this->placement->update([
                'pkl_field' => $this->pkl_field
            ]);

            // Refresh data setelah update
            $this->loadPlacement();
        }
    }

    public function render()
    {
        return view('livewire.student.dudika');
    }
}
