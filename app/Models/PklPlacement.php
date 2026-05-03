<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PklPlacement extends Model
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

    public function journals()
    {
        return $this->hasMany(Journal::class, 'pkl_placement_id');
    }

    public function monitorings()
    {
        return $this->hasMany(Monitoring::class, 'pkl_placement_id');
    }

    public function pklAssessment()
    {
        return $this->hasOne(PklAssessment::class);
    }
}
