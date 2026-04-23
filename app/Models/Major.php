<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $guarded = [];

    public function assessmentSchemes()
    {
        return $this->hasMany(AssessmentScheme::class);
    }
}
