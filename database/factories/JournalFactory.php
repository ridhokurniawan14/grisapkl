<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class JournalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pkl_placement_id' => $this->faker->numberBetween(5, 11), // dari data lu
            'date' => $this->faker->dateTimeBetween('2026-06-22', '2026-12-22')->format('Y-m-d'),
            'time' => $this->faker->time(),
            'attendance_photo_path' => null,
            'attend_status' => $this->faker->randomElement(['Hadir', 'Sakit', 'Izin']),
            'activity' => $this->faker->sentence(6),
            'photo_path' => 'journals/' . $this->faker->uuid . '.png',
            'latitude' => $this->faker->latitude(-8.3, -6.0),
            'longitude' => $this->faker->longitude(106.7, 114.4),
            'is_valid' => 1,
            'revision_note' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
