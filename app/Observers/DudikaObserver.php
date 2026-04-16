<?php

namespace App\Observers;

use App\Models\Dudika;
use App\Models\User;

class DudikaObserver // <-- Ini yang tadi lupa diganti bro!
{
    public function saved(Dudika $dudika): void
    {
        // Cek kelengkapan data wajib sebelum buat akun
        if ($dudika->supervisor_name && $dudika->supervisor_phone) {

            // Ambil 5 digit terakhir nomor HP untuk Username & Password
            $phone = preg_replace('/[^0-9]/', '', $dudika->supervisor_phone);
            $lastFive = substr($phone, -5);

            // Cari User berdasarkan email
            $user = User::firstOrNew(
                ['email' => $lastFive . '@smkpgri1giri.sch.id']
            );

            // Update nama sesuai pembimbing terbaru
            $user->name = $dudika->supervisor_name;

            // Jika ini user baru (belum ada di database), baru set password default-nya
            if (!$user->exists) {
                $user->password = bcrypt($lastFive);
            }

            $user->save();

            // Pastikan punya role Dudika
            if (!$user->hasRole('Dudika')) {
                $user->assignRole('Dudika');
            }

            // Hubungkan user_id ke DUDIKA tanpa memicu loop observer
            $dudika->updateQuietly(['user_id' => $user->id]);
        }
    }
}
