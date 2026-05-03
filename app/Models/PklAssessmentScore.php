<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PklAssessmentScore extends Model
{
    use LogsActivity;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function pklAssessment()
    {
        return $this->belongsTo(PklAssessment::class);
    }

    public function assessmentIndicator()
    {
        return $this->belongsTo(AssessmentIndicator::class);
    }
}
