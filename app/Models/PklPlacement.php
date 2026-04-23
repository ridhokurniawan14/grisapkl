<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklPlacement extends Model
{
    protected $guarded = [];

    // Relasi ke Data Master
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function dudika()
    {
        return $this->belongsTo(Dudika::class);
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
    public function assessmentScheme()
    {
        return $this->belongsTo(AssessmentScheme::class);
    }
}
