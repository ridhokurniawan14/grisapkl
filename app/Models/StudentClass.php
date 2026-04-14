<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    public function major()
    {
        return $this->belongsTo(Major::class);
    }
}
