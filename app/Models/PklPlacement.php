<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklPlacement extends Model
{
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function dudika()
    {
        return $this->belongsTo(Dudika::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
