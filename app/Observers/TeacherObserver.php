<?php

namespace App\Observers;

use App\Models\Teacher;
use App\Models\User;

class TeacherObserver
{
    public function saved(Teacher $teacher): void
    {
        // Skip kalau datang dari proses import (importer handle sendiri)
        if (app()->bound('importing.teacher')) {
            return;
        }

        $fullNameWithTitle = $teacher->title
            ? $teacher->name . ', ' . $teacher->title
            : $teacher->name;

        if (!$teacher->user_id && $teacher->name) {

            // 1. Ambil kata depan untuk Email
            $firstName = strtolower(explode(' ', trim($teacher->name))[0]);
            $baseUsername = preg_replace('/[^a-z0-9]/', '', $firstName);
            if (empty($baseUsername)) $baseUsername = 'guru';

            $username = $baseUsername;
            $email = $username . '@smkpgri1giri.sch.id';
            $counter = 1;

            while (User::where('email', $email)->exists()) {
                $username = $baseUsername . $counter;
                $email = $username . '@smkpgri1giri.sch.id';
                $counter++;
            }

            // 2. Password 5 digit HP
            $phone = preg_replace('/[^0-9]/', '', $teacher->phone ?? '12345');
            $password = substr($phone, -5);
            if (strlen($password) < 5) {
                $password = '12345';
            }

            // 3. Buat User
            $user = User::create([
                'name' => $fullNameWithTitle,
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
