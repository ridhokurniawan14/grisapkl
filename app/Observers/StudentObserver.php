<?php

namespace App\Observers;

use App\Models\Student;

class StudentObserver
{
    /**
     * Handle the Student "created" event.
     */
    public function created(Student $student): void
    {
        // Ambil password dari tgl lahir (format dmy: 24122002)
        $password = $student->birth_date ? $student->birth_date->format('dmY') : '12345678';

        $user = \App\Models\User::create([
            'name' => $student->user->name, // Mengambil nama dari input user
            'email' => $student->nis . '@smkpgri1giri.sch.id', // Username pakai NIS
            'password' => bcrypt($password),
        ]);

        $user->assignRole('Siswa');

        // Update link student ke user yang baru dibuat
        $student->updateQuietly(['user_id' => $user->id]);
    }

    /**
     * Handle the Student "updated" event.
     */
    public function updated(Student $student): void
    {
        //
    }

    /**
     * Handle the Student "deleted" event.
     */
    public function deleted(Student $student): void
    {
        //
    }

    /**
     * Handle the Student "restored" event.
     */
    public function restored(Student $student): void
    {
        //
    }

    /**
     * Handle the Student "force deleted" event.
     */
    public function forceDeleted(Student $student): void
    {
        //
    }
}
