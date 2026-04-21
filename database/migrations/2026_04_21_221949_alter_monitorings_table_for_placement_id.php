<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitorings', function (Blueprint $table) {
            // 1. Buang Foreign Key dan Kolom lama
            $table->dropConstrainedForeignId('teacher_id');
            $table->dropConstrainedForeignId('dudika_id');

            // 2. Tambahkan Kolom Relasi Baru (pkl_placement_id)
            // Diletakkan setelah monitoring_schedule_id agar susunannya rapi di database
            $table->foreignId('pkl_placement_id')
                ->after('monitoring_schedule_id')
                ->constrained('pkl_placements')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('monitorings', function (Blueprint $table) {
            // Rollback jika terjadi kesalahan
            $table->dropConstrainedForeignId('pkl_placement_id');
            $table->foreignId('teacher_id')->constrained('teachers');
            $table->foreignId('dudika_id')->constrained('dudikas');
        });
    }
};
