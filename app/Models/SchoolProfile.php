<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SchoolProfile extends Model
{
    use HasFactory;

    // Membuka gembok agar semua kolom bisa diisi oleh Filament
    protected $guarded = [];

    protected static function booted()
    {
        // Saat data di-update (Ubah)
        static::updating(function ($profile) {
            // Hapus Logo lama jika ada yang baru
            if ($profile->isDirty('logo_path') && $profile->getOriginal('logo_path')) {
                Storage::disk('public')->delete($profile->getOriginal('logo_path'));
            }
            // Hapus Tanda Tangan lama jika ada yang baru
            if ($profile->isDirty('signature_path') && $profile->getOriginal('signature_path')) {
                Storage::disk('public')->delete($profile->getOriginal('signature_path'));
            }
        });

        // Saat data dihapus permanen (Delete) - Jaga-jaga kalau record Profil dihapus
        static::deleted(function ($profile) {
            if ($profile->logo_path) {
                Storage::disk('public')->delete($profile->logo_path);
            }
            if ($profile->signature_path) {
                Storage::disk('public')->delete($profile->signature_path);
            }
        });
    }
}
