<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dudika extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::deleting(function (Dudika $dudika) {
            // Sekarang user_id sudah terisi, ini akan jalan dengan benar
            if ($dudika->user_id && $dudika->user) {
                $dudika->user()->delete();
            }
        });
    }

    public function getIsCompleteAttribute(): bool
    {
        return !empty($this->name) &&
            !empty($this->address) &&
            !empty($this->head_name) &&
            !empty($this->supervisor_name) &&
            !empty($this->supervisor_phone);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
