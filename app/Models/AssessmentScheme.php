<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentScheme extends Model
{
    protected $guarded = [];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }
    public function assessmentIndicators()
    {
        return $this->hasMany(AssessmentIndicator::class);
    }
    public function pklPlacements()
    {
        return $this->hasMany(PklPlacement::class);
    }
}
