<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklAssessment extends Model
{
    protected $guarded = [];
    public function pklPlacement()
    {
        return $this->belongsTo(PklPlacement::class);
    }
    public function scores()
    {
        return $this->hasMany(PklAssessmentScore::class);
    }
}
