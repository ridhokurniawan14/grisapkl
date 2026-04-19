<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    use HasFactory;

    // Membuka gembok agar semua kolom bisa diisi oleh Filament
    protected $guarded = [];
}
