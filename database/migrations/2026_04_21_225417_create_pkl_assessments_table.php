<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pkl_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_placement_id')->constrained('pkl_placements')->cascadeOnDelete();
            $table->text('attendance_notes')->nullable(); // Catatan Kehadiran
            $table->text('assessment_notes')->nullable(); // Catatan Penilaian (Softskill/Hardskill dll)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkl_assessments');
    }
};
