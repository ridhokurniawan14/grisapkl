<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentElement extends Model
{
    protected $guarded = [];

    public function assessmentIndicators()
    {
        return $this->hasMany(AssessmentIndicator::class);
    }
}
