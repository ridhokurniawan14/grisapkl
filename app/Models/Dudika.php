<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dudika extends Model
{
    protected $guarded = [];

    public function getIsCompleteAttribute(): bool
    {
        return !empty($this->name) &&
            !empty($this->address) &&
            !empty($this->head_name) && // Pimpinan wajib untuk TTD
            !empty($this->supervisor_name) && // Pembimbing wajib
            !empty($this->supervisor_phone);
    }
}
