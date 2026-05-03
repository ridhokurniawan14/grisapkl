<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class AssessmentScheme extends Model
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
