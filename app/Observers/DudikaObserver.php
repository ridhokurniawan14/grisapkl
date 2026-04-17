<?php

namespace App\Observers;

use App\Models\Dudika;
use App\Models\User;

class DudikaObserver
{
    public function saved(Dudika $dudika): void
    {
        // Skip kalau datang dari proses import (importer handle sendiri)
        if (app()->bound('importing.dudika')) {
            return;
        }

        if ($dudika->supervisor_name && $dudika->supervisor_phone) {
            $phone    = preg_replace('/[^0-9]/', '', $dudika->supervisor_phone);
            $lastFive = substr($phone, -5);
            $email    = $lastFive . '@smkpgri1giri.sch.id';

            $user       = User::firstOrNew(['email' => $email]);
            $user->name = $dudika->supervisor_name;

            if (!$user->exists) {
                $user->password = bcrypt($lastFive);
            }

            $user->save();

            if (!$user->hasRole('Dudika')) {
                $user->assignRole('Dudika');
            }

            $dudika->updateQuietly(['user_id' => $user->id]);
        }
    }

    public function deleted(Dudika $dudika): void
    {
        if ($dudika->user_id) {
            $user = User::find($dudika->user_id);
            $user?->delete();
        }
    }
}
