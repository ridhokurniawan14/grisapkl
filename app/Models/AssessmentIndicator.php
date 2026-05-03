<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class AssessmentIndicator extends Model
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

    public function assessmentElement()
    {
        return $this->belongsTo(AssessmentElement::class);
    }

    public function assessmentScheme()
    {
        return $this->belongsTo(AssessmentScheme::class);
    }
}
