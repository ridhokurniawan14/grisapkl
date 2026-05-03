<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Dudika extends Model
{
    use LogsActivity;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::deleting(function (Dudika $dudika) {
            if ($dudika->user_id && $dudika->user) {
                $dudika->user()->delete();
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function getIsCompleteAttribute(): bool
    {
        return !empty($this->name) &&
            !empty($this->address) &&
            !empty($this->supervisor_name) &&
            !empty($this->supervisor_phone) &&
            !empty($this->head_name) &&
            !empty($this->latitude) &&
            !empty($this->longitude);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pklPlacements()
    {
        return $this->hasMany(PklPlacement::class);
    }
}
