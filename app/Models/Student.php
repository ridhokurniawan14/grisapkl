<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
