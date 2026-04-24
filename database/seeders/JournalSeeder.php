<?php

namespace Database\Seeders; // ← ganti namespace

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder; // ← extend Seeder, bukan Factory

class JournalSeeder extends Seeder // ← rename class
{
    public function run(): void
    {
        $pklIds = [12, 13, 14];

        foreach ($pklIds as $pklId) {

            $start = Carbon::create(2026, 6, 22);
            $end   = Carbon::create(2026, 12, 22);

            $dates = [];

            while ($start->lte($end)) {
                $dates[] = $start->copy();
                $start->addDay();
            }

            $alphaDates = collect($dates)->random(5)->map(fn($d) => $d->format('Y-m-d'))->toArray();

            foreach ($dates as $dateObj) {
                $date = $dateObj->format('Y-m-d');

                if (in_array($date, $alphaDates)) {
                    continue;
                }

                DB::table('journals')->insert([
                    'pkl_placement_id' => $pklId,
                    'date' => $date,
                    'time' => fake()->time(),
                    'attendance_photo_path' => 'attendance/' . Str::uuid() . '.png',
                    'attend_status' => fake()->randomElement(['Hadir', 'Sakit', 'Izin', 'Libur']),
                    'activity' => fake()->sentence(6),
                    'photo_path' => 'journals/' . Str::uuid() . '.png',
                    'latitude' => fake()->latitude(-8.3, -6.0),
                    'longitude' => fake()->longitude(106.7, 114.4),
                    'is_valid' => 1,
                    'revision_note' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
