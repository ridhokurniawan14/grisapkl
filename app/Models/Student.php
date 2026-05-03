<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Student extends Model
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

    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function getIsCompleteAttribute(): bool
    {
        return !empty($this->nisn) &&
            !empty($this->phone) &&
            !empty($this->birth_place) &&
            !empty($this->birth_date) &&
            !empty($this->religion) &&
            !empty($this->address) &&
            !empty($this->father_name) &&
            !empty($this->mother_name);
    }

    public function pklPlacements()
    {
        return $this->hasMany(PklPlacement::class);
    }
}
