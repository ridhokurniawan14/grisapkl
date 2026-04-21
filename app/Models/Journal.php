<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Journal extends Model
{
    use HasFactory;

    // Membuka gembok agar semua kolom bisa diisi oleh Filament
    protected $guarded = [];

    public function pklPlacement()
    {
        return $this->belongsTo(PklPlacement::class, 'pkl_placement_id');
    }
    protected static function booted()
    {
        // Saat data di-update (Ubah)
        static::updating(function ($journal) {
            // Cek apakah foto kegiatan sore diubah?
            if ($journal->isDirty('photo_path') && $journal->getOriginal('photo_path')) {
                Storage::disk('public')->delete($journal->getOriginal('photo_path'));
            }
            // Cek apakah foto absen pagi diubah?
            if ($journal->isDirty('attendance_photo_path') && $journal->getOriginal('attendance_photo_path')) {
                Storage::disk('public')->delete($journal->getOriginal('attendance_photo_path'));
            }
        });

        // Saat data dihapus permanen (Delete)
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
