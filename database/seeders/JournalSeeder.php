<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Journal;

class JournalSeeder extends Seeder
{
    public function run(): void
    {
        Journal::factory()->count(50)->create();
    }
}
