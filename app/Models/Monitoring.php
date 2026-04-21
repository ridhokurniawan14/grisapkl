<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Monitoring extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Relasi ke Jadwal Monitoring
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
        // Saat data di-update (Ubah)
        static::updating(function ($monitoring) {
            // Cek apakah foto kegiatan sore diubah?
            if ($monitoring->isDirty('photo_path') && $monitoring->getOriginal('photo_path')) {
                Storage::disk('public')->delete($monitoring->getOriginal('photo_path'));
            }
        });

        // Saat data dihapus permanen (Delete)
        static::deleted(function ($monitoring) {
            if ($monitoring->photo_path) {
                Storage::disk('public')->delete($monitoring->photo_path);
            }
        });
    }
}
