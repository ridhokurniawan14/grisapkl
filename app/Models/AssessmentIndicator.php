<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentIndicator extends Model
{
    protected $guarded = [];

    public function assessmentElement()
    {
        return $this->belongsTo(AssessmentElement::class);
    }

    public function assessmentScheme()
    {
        return $this->belongsTo(AssessmentScheme::class);
    }
}
