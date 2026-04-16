<?php

namespace App\Observers;

use App\Models\Dudika;
use App\Models\User;

class DudikaObserver // <-- Ini yang tadi lupa diganti bro!
{
    public function saved(Dudika $dudika): void
    {
        if ($dudika->supervisor_name && $dudika->supervisor_phone) {
            $phone = preg_replace('/[^0-9]/', '', $dudika->supervisor_phone);
            $lastFive = substr($phone, -5);

            // Buat prefix dari nama DUDIKA (ambil 5 huruf pertama, buang spasi)
            $namePrefix = strtolower(substr(preg_replace('/[^A-Za-z0-9]/', '', $dudika->name), 0, 5));

            // Email jadi lebih unik: contoh ptwah12345@smkpgri1giri.sch.id
            $email = $lastFive . '@smkpgri1giri.sch.id';

            $user = \App\Models\User::firstOrNew(['email' => $email]);
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
        // Jika data DUDIKA dihapus, hapus juga akun user-nya
        if ($dudika->user_id) {
            $user = User::find($dudika->user_id);
            if ($user) {
                $user->delete();
            }
        }
    }
}
