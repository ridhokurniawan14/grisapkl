<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Teacher extends Model
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pklPlacements()
    {
        return $this->hasMany(PklPlacement::class);
    }
    // public function monitoringSchedules()
    // {
    //     return $this->hasMany(\App\Models\MonitoringSchedule::class, 'teacher_id');
    // }
    // Hapus relasi monitoringSchedules yang sebelumnya error, ganti dengan ini:
    public function monitorings()
    {
        // Parameter: (Model Tujuan, Model Perantara, FK di Perantara, FK di Tujuan, LK di Asal, LK di Perantara)
        return $this->hasManyThrough(
            \App\Models\Monitoring::class,    // Model Tujuan (tabel riwayat monitoring)
            \App\Models\PklPlacement::class,  // Model Jembatan (penempatan PKL)
            'teacher_id',                     // Foreign key di tabel pkl_placements
            'pkl_placement_id',               // Foreign key di tabel monitorings
            'id',                             // Local key di tabel teachers
            'id'                              // Local key di tabel pkl_placements
        );
    }
}
