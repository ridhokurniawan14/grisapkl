<?php

namespace App\Observers;

use App\Models\Dudika;
use App\Models\User;

class DudikaObserver
{
    public function saved(Dudika $dudika): void
    {
        // Kosongkan! 
        // Pembuatan akun User sekarang ditangani langsung oleh Form (saveRelationshipsUsing)
        // dan DudikaImporter. Jauh lebih aman dan tidak bentrok!
    }

    public function deleted(Dudika $dudika): void
    {
        if ($dudika->user_id) {
            $user = User::find($dudika->user_id);
            $user?->delete();
        }
    }
}
