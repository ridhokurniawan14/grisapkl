<?php

namespace App\Observers;

use App\Models\Teacher;
use App\Models\User;

class TeacherObserver
{
    public function saved(Teacher $teacher): void
    {
        // Ambil Nama Terang (Nama + Gelar jika ada)
        $fullNameWithTitle = $teacher->title
            ? $teacher->name . ', ' . $teacher->title
            : $teacher->name;

        if (!$teacher->user_id && $teacher->name) {
            // Logic Username: nama tanpa spasi
            $baseUsername = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $teacher->name));
            $username = $baseUsername;
            $email = $username . '@smkpgri1giri.sch.id';
            $counter = 1;

            while (User::where('email', $email)->exists()) {
                $username = $baseUsername . $counter;
                $email = $username . '@smkpgri1giri.sch.id';
                $counter++;
            }

            // Password Default = Full No HP
            $password = preg_replace('/[^0-9]/', '', $teacher->phone ?? '12345678');
            if (empty($password)) $password = '12345678';

            $user = User::create([
                'name' => $fullNameWithTitle, // Simpan nama + gelar
                'email' => $email,
                'password' => bcrypt($password),
            ]);

            $user->assignRole('Guru');
            $teacher->updateQuietly(['user_id' => $user->id]);
        } elseif ($teacher->user_id) {
            $user = User::find($teacher->user_id);
            if ($user) {
                $user->update(['name' => $fullNameWithTitle]);
            }
        }
    }

    public function deleted(Teacher $teacher): void
    {
        if ($teacher->user_id) {
            $user = User::find($teacher->user_id);
            if ($user) $user->delete();
        }
    }
}
