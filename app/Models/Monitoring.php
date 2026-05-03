<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Monitoring extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function monitoringSchedule()
    {
        return $this->belongsTo(MonitoringSchedule::class);
    }

    public function pklPlacement()
    {
        return $this->belongsTo(PklPlacement::class, 'pkl_placement_id');
    }

    protected static function booted()
    {
        static::updating(function ($monitoring) {
            if ($monitoring->isDirty('photo_path') && $monitoring->getOriginal('photo_path')) {
                Storage::disk('public')->delete($monitoring->getOriginal('photo_path'));
            }
        });

        static::deleted(function ($monitoring) {
            if ($monitoring->photo_path) {
                Storage::disk('public')->delete($monitoring->photo_path);
            }
        });
    }
}
