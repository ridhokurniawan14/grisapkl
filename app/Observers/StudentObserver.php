<?php

namespace App\Observers;

use App\Models\Student;
use App\Models\User;

class StudentObserver
{
    public function saved(Student $student): void
    {
        // Abaikan jika sedang proses Import Excel
        if (app()->bound('importing.student')) {
            return;
        }

        if (!$student->user_id && $student->name && $student->nis) {
            // Potong NIS berdasarkan garis miring "/", ambil bagian pertama (index 0)
            $shortNis = explode('/', $student->nis)[0];

            $email = $shortNis . '@smkpgri1giri.sch.id';
            $password = $shortNis; // Password pakai NIS pendek

            $user = User::firstOrNew(['email' => $email]);
            $user->name = $student->name;

            if (!$user->exists) {
                $user->password = bcrypt($password);
            }

            $user->save();

            if (!$user->hasRole('Siswa')) {
                $user->assignRole('Siswa');
            }

            $student->updateQuietly(['user_id' => $user->id]);
        } elseif ($student->user_id) {
            $user = User::find($student->user_id);
            if ($user) $user->update(['name' => $student->name]);
        }
    }

    public function deleted(Student $student): void
    {
        if ($student->user_id) {
            $user = User::find($student->user_id);
            if ($user) $user->delete();
        }
    }
}
