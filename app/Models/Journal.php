<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Journal extends Model
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

    public function pklPlacement()
    {
        return $this->belongsTo(PklPlacement::class, 'pkl_placement_id');
    }

    protected static function booted()
    {
        static::updating(function ($journal) {
            if ($journal->isDirty('photo_path') && $journal->getOriginal('photo_path')) {
                Storage::disk('public')->delete($journal->getOriginal('photo_path'));
            }
            if ($journal->isDirty('attendance_photo_path') && $journal->getOriginal('attendance_photo_path')) {
                Storage::disk('public')->delete($journal->getOriginal('attendance_photo_path'));
            }
        });

        static::deleted(function ($journal) {
            if ($journal->photo_path) {
                Storage::disk('public')->delete($journal->photo_path);
            }
            if ($journal->attendance_photo_path) {
                Storage::disk('public')->delete($journal->attendance_photo_path);
            }
        });
    }
}
