<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;           // ← tambah ini
use Spatie\Activitylog\Traits\LogsActivity;  // ← tambah ini

class SchoolProfile extends Model
{
    use HasFactory, LogsActivity; // ← uncomment LogsActivity-nya

    protected $guarded = [];

    protected static function booted()
    {
        static::updating(function ($profile) {
            if ($profile->isDirty('logo_path') && $profile->getOriginal('logo_path')) {
                Storage::disk('public')->delete($profile->getOriginal('logo_path'));
            }
            if ($profile->isDirty('signature_path') && $profile->getOriginal('signature_path')) {
                Storage::disk('public')->delete($profile->getOriginal('signature_path'));
            }
        });

        static::deleted(function ($profile) {
            if ($profile->logo_path) {
                Storage::disk('public')->delete($profile->logo_path);
            }
            if ($profile->signature_path) {
                Storage::disk('public')->delete($profile->signature_path);
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
