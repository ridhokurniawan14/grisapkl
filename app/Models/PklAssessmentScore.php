<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklAssessmentScore extends Model
{
    protected $guarded = [];
    public function pklAssessment()
    {
        return $this->belongsTo(PklAssessment::class);
    }
    public function assessmentIndicator()
    {
        return $this->belongsTo(AssessmentIndicator::class);
    }
}
