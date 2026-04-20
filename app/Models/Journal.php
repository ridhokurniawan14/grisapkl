<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    // Membuka gembok agar semua kolom bisa diisi oleh Filament
    protected $guarded = [];

    // Pastikan fungsi ini ADA di dalam model Journal.php kamu bro
    public function pklPlacement()
    {
        return $this->belongsTo(PklPlacement::class, 'pkl_placement_id');
    }
}
